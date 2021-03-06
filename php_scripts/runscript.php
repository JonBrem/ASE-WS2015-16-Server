<?php
	/* this should be running continuously. */
	require_once("util/db_connection.php");
	require_once("util/config.php");
	require_once("util/status_codes.php");
	require_once("util/curl_request_async.php");

    /**
     * The central class in this application.
     * Periodically performs the main routine if runscript.php was once opened.
     * calling it more often does not do any harm; the program will always run
     * at the same frequency.
     */
	class MediaTextRecognitionLogic {

		/**
		 * Performs the main routine of this application.
		 * Controls the queue & what happens to the item that is being processed at the moment (If there is one).
		 * Also checks if any errors occurred.
		 * <b>Caution!</b> May not be executed, depending on when the method was last called. 
		 */
		public function run() {
			$lastRunTime = $this->readTime();

			if($this->checkTimeDifference($lastRunTime)) {
				$this->writeTime();

				$conn = getDBConnection();

				if(!$conn->connect_error) {
					if($this->wasProbablyInterrupted($lastRunTime)) {
						$this->resetStatus($conn);
					}

					$this->checkQueueErrors($conn);
					$this->checkCurrentTextRecognition($conn);
					$this->checkQueue($conn);
					$this->checkQueueDownloads($conn);
				} else {
					error_log("runscript in media text recognition can't connect to database.");
				}

				$conn->close();
				return true;
			} else { # not enough time has passed for this to run again.
				return false;
			}
		}

		/**
		 * If there was a severe interruption (e.g. the server was shut down), some items in the queue may have a faulty status
		 * and be stuck in one of the "currently doing something"-status without anything actually being done.
		 * If the script was last called more than an hour ago, this method assumes that was true and resets all these status.
		 * 
		 * 
		 * @param $conn active SQL connection
		 */
		public function resetStatus($conn) {
			$results = $conn->query('SELECT * FROM queue WHERE `status` IN ("' . STATUS_BEING_PROCESSED . '","' . STATUS_SEGMENTING_VIDEO . '","' . STATUS_EVALUATING_WORDS . '","' . STATUS_DOWNLOADING . '")');

			if($results->num_rows > 0) {
				while(($result = $results->fetch_assoc()) != null) {
					$newStatus = null;
					if ($result["status"] == STATUS_BEING_PROCESSED) $newStatus = STATUS_FINISHED_SEGMENTING_VIDEO;
					else if ($result["status"] == STATUS_SEGMENTING_VIDEO) $newStatus = STATUS_DOWNLOADED;
					else if ($result["status"] == STATUS_EVALUATING_WORDS) $newStatus = STATUS_FINISHED_PROCESSING;
					else if ($result["status"] == STATUS_DOWNLOADING) $newStatus = STATUS_IN_QUEUE;

					if($newStatus != null) {
						$conn->query("UPDATE queue SET `status`=\"$newStatus\" WHERE `id`=" . $result["id"]);
					}
				}
			}
		}

		/**
		 * If a media item is in the main routine, this looks at its progress
		 * and moves it into the next stage (segmenting --> parsing --> evaluating), if required.
		 * If no item is in the main routine but there is one in the queue
		 * ready to be processed, this method adds it to the queue.
		 * @param $conn active SQL connection
		 */
		private function checkCurrentTextRecognition($conn) {
			// todo make this more of an "if this then that"-pipeline
			$this->checkIfSegmentingVideoFinished($conn);
			$this->checkIfParsingFinished($conn);
			$this->checkIfEvaluationFinished($conn);

			if(!$this->currentlyProcessingImage($conn)) {
				$item = $this->getItemToProcess($conn);
				if($item !== false) {
					$this->segmentVideo($item, $conn);
				}
			}
		}

		/**
		 * If the media item that is currently being processed is at the stage "finished_processing", the word evaluation will start.
		 * @param $conn active SQL connection
		 */
		private function checkIfParsingFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_FINISHED_PROCESSING ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->evaluateRecongizedWords($row['media_id'], $conn);
			}
		}

		/**
		 * If the media item that is currently being processed is at the stage "ready_for_history", it will be removed from the queues
		 * @param $conn active SQL connection
		 */
		private function checkIfEvaluationFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_READY_FOR_HISTORY ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->moveToHistory($row['media_id'], $conn);
			}
		}

		/**
		 * If the media item that is currently being processed is at the stage "finished_segmenting_video", the processing with the executable will start.
		 * @param $conn active SQL connection
		 */
		private function checkIfSegmentingVideoFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_FINISHED_SEGMENTING_VIDEO ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->parseVideo($row['media_id'], $conn);
			}
		}

		/**
		 * Calls the evalute_words subroutine.
		 * @param $mediaID: ID of the item that is currently being processed
		 * @param $conn active SQL connection
		 */
		private function evaluateRecongizedWords($mediaID, $conn) {
			$evaluationScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/evaluate_words.php";
			$parseOutput = $this->getAbsolutePathOnServer() . "/../video_downloads/output_$mediaID.json";
			$javaExec = $this->getAbsolutePathOnServer() . "/../java_tool/ASE-WS2015-16-WordValidator.jar";
			$javaOutput = $this->getAbsolutePathOnServer() . "/../video_downloads/java_output_$mediaID.txt";
			$javaToolPath = $this->getAbsolutePathOnServer() . "/../java_tool";
			curl_request_async(
				$evaluationScript,
				array(
					"media_id" => $mediaID, 
					"json_file" => $parseOutput,
					"output_file" => $javaOutput,
					"java_exec_path" => $javaExec,
					"java_tool_folder" => $javaToolPath
					),
				"GET"
			);
		}

		/**
		 * Calls the parse_video subroutine.
		 * @param $mediaID: ID of the item that is currently being processed
		 * @param $conn active SQL connection
		 */
		private function parseVideo($mediaID, $conn) {
			$parseVideoScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/parse_video.php";
			$outputFilePath = $this->getAbsolutePathOnServer() . "/../video_downloads/output_$mediaID.json";
			$videosFolder = $this->getAbsolutePathOnServer() . "/../video_downloads";
			$segmentedVideoPath = "$videosFolder/video_segments";
			$trainingFilesFolder = $this->getAbsolutePathOnServer . "/../cpp_tool/";

			curl_request_async(
				$parseVideoScript, 
				array(
					"segmented_video_path" => $segmentedVideoPath,
					"output_file" => $outputFilePath,
					"media_id" => $mediaID,
					"training_files_folder" => $trainingFilesFolder
					), 
				"GET");
		}

		/**
		 * Removes the specified item from the queue, updates its status in the media table (to "history").
		 * After that is done, updates the positions of the other items in the queue.
		 */
		private function moveToHistory($mediaID, $conn) {
			$conn->query("DELETE FROM queue WHERE media_id=$mediaID");
			$conn->query("UPDATE media SET status=\"" . STATUS_HISTORY . "\" WHERE id=$mediaID");

			$this->fixQueueItemPositions($conn);
		}

		/**
		 * Removes any gaps in numbering of the items in the queue etc.; previous order will be kept,
		 * but the numbers will start at 1 and not have gaps.
		 */
		private function fixQueueItemPositions($conn) {
			// update queue item numbers!!
			$queueItems = $conn->query("SELECT * FROM queue ORDER BY position ASC");
			if($queueItems->num_rows > 0) {
				$index = 1;
				while($queueItem = $queueItems->fetch_assoc()) {
					$conn->query("UPDATE queue SET position=$index WHERE id=$queueItem[id]");
					$index++;
				}
			}
		}

		/**
		 * @return true if an item is currently being processed, false if not.
		 */
		private function currentlyProcessingImage($conn) {
			$condition = " `queue`.`status`=\"" . STATUS_BEING_PROCESSED . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_FINISHED_PROCESSING . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_SEGMENTING_VIDEO . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_FINISHED_SEGMENTING_VIDEO . "\"";

			$sqlResult = $conn->query("SELECT `queue`.`id` FROM queue WHERE $condition");

			return ($sqlResult->num_rows > 0);
		}

		/**
		 * if an item is at the top of the queue with the status "downloaded", returns that item.
		 * <b>Verify</b> that there is no item that is already being processed, though!
		 * @return false if no item is being processed;
		 * an array containing IDs of the item in the queue and media tables as keys if there is.
		 */
		private function getItemToProcess($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`id` AS queue_id, `media`.`id` FROM queue LEFT JOIN media" 
				. " ON `queue`.`media_id`=`media`.`id` WHERE `queue`.`status`=\"" . STATUS_DOWNLOADED . "\" AND `queue`.`position`=1");

			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
 				$item = array("media_id" => $row["id"], "queue_id" => $row["queue_id"]);
				return $item;
			} else {
				return false;
			}
		}



		/**
		 * Calls the segment_video subroutine.
		 * @param $item array that must have the keys "media_id" and "queue_id"
		 */
		private function segmentVideo($item) {
			$segmentVideoScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/segment_video.php";
			
			$videosFolder = $this->getAbsolutePathOnServer() . "/../video_downloads";

			$videoFilePath = "$videosFolder/$item[media_id].mp4"; 
			$segmentedVideoPath = "$videosFolder/video_segments";

			curl_request_async(
				$segmentVideoScript,
				array(
					"media_id" => $item["media_id"],
					"queue_id" => $item["queue_id"],
					"video_file_path" => $videoFilePath,
					"segmented_video_path" => $segmentedVideoPath
					),
				"GET"
			);
		}

		/**
		 * If enough items are in the queue, this does nothing except (maybe) fix the sorting of the items.
		 * If there are any unprocessed items in the media table and less than 10 in the queue,
		 * this method will add those to the queue.
		 */
		private function checkQueue($conn) {
			$this->fixQueueItemPositions($conn);

			$numOfItemsInQueue = $this->getNumOfItemsInQueue($conn);
			if($numOfItemsInQueue < 10) {
				$items = $this->getNextItemsForQueue($conn, 10 - $numOfItemsInQueue, $numOfItemsInQueue);
				if(sizeof($items) > 0) {
					$this->appendItemsToQueue($conn, $items);
				}
			} 
		}

		/**
		 * If the videos of top 3 items in the queue (including the one being processed, if there is one) have not been
		 * downloaded already, they will be in this method.
		 */
		private function checkQueueDownloads($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id`, `media`.`video_url` FROM queue LEFT JOIN media ON `queue`.`media_id` = `media`.`id` WHERE (`position`<=3 AND `queue`.`status`='". STATUS_IN_QUEUE ."')");
			$this->createVideoDownloadsDirIfNotExists();		

			if($sqlResult->num_rows > 0) {
				while($row = $sqlResult->fetch_assoc()) {
					if($conn->query("UPDATE queue SET `status`=\"". STATUS_DOWNLOADING ."\" WHERE `media_id` = $row[media_id]")) {

						$this->downloadInBackground($row["media_id"], $row["video_url"], $this->getAbsolutePathOnServer() . "/../video_downloads/$row[media_id].mp4");
					}
				}
			}
		}

		/**
		 * Calls the video_download subroutine.
		 * @param $mediaId: ID of the item that is currently being processed
		 * @param $video video (.mp4 or similar) URL oon some server.
		 * @param $downloadTo Path that the video file will have on the machine this script is executed on.
		 */
		private function downloadInBackground($mediaId, $video, $downloadTo) {
			$videoDonwloadScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/video_download.php";
			curl_request_async($videoDonwloadScript, array(
					"video_url" => $video,
					"download_to" => $downloadTo,
					"item_id" => $mediaId
				), "GET");
		}

		/**
		 * @return the URL-path of this file is in.
		 * E.g., if this file is at http://www.example.com/meow/runscript.php, the method will return
		 * http://www.example.com/meow/ ; will only work for http
		 */
		private function getPhpScriptsPathOnServer() {
			$fileName = pathinfo(__FILE__, PATHINFO_FILENAME) . "php";
			$currentScriptOnServer = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			return substr($currentScriptOnServer, 0, strlen($currentScriptOnServer) - strlen($fileName) - 1);
		}

		/**
		 * @return the folder this file is in (on windows: C:\\something\\something...\\php_scripts\\runscript.php, 
		 * others: /something/somethig.../php_scripts/runscript.php)
		 */
		private function getAbsolutePathOnServer() {
			return realpath(dirname(__FILE__));
		}

		private function getNumOfItemsInQueue($conn) {
			$result = $conn->query("SELECT COUNT(*) FROM queue");
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row["COUNT(*)"];
			} else {
				return 0;
			}
		}

		/**
		 * If there are any items in the media table that have not been processed yet and are not in the queue,
		 * this will return 1 to $howMany of them.
		 * @param $conn active SQL connection
		 * @param $howMany how many items should be retrieved (max! might be fewer.)
		 * @param $positionOffset the current highest position in the queue.
		 */
		private function getNextItemsForQueue($conn, $howMany, $positionOffset) {
			$sqlResult = $conn->query("SELECT id FROM media WHERE (status='". STATUS_CRAWLED ."') ORDER BY ID ASC LIMIT $howMany");
			$nextItems = array();

			if($sqlResult->num_rows > 0) {
				$i = 1;
				while($row = $sqlResult->fetch_assoc()) {
					$nextItems[] = array(
							"media_id" => $row["id"],
							"status" => "in_queue",
							"position" => $positionOffset + $i
						);
					$i++;
				}
			}

			return $nextItems;
		}

		/**
		 * @param $conn active SQL connection
		 * @param $items array of arrays (media_id, status, position) that will be added to the queue.
		 */ 
		private function appendItemsToQueue($conn, $items) {
			if(sizeof($items) > 0) {
				$insertString = "INSERT INTO queue (`media_id`,`status`,`position`) VALUES ";

				for($i = 0; $i < sizeof($items); $i++) {
					$itemString = "(" . 
						$items[$i]["media_id"] . "," . 
						"\"" . $items[$i]["status"] . "\"," .
						$items[$i]["position"] . ")";
					if($i != sizeof($items) - 1) $itemString .= ",";

					if($conn->query("UPDATE media SET `status`='". STATUS_IN_QUEUE ."' WHERE (`id`=" . $items[$i]["media_id"] . ");") === TRUE) {
						$insertString .= $itemString;
					}
				}

				$insertString .= ";";

				$conn->query($insertString);
			}
		}

		/**
		 * creates a new directory where most of the output from the routines in the application go to.
		 */
		private function createVideoDownloadsDirIfNotExists() {
			$videoDownloadPath = realpath(dirname(__FILE__));
			$videoDownloadPath .= "/../video_downloads";

			if(!file_exists($videoDownloadPath)) {
				mkdir($videoDownloadPath, 0777);
			}

		}

		/**
		 * checks if there are any items with errors (see status codes) in the queue; if so,
		 * removes those items and updates the positions of the other ones.
		 */
		private function checkQueueErrors($conn) {
			$errors = $conn->query("SELECT * FROM queue WHERE status IN (" . ERRORS_SQL . ")");
			if($errors->num_rows > 0) {
				while($error = $errors->fetch_assoc()) {
					$this->removeErroneousQueueItem($error["media_id"], $error["status"], $conn);
				}
			}
		}

		private function removeErroneousQueueItem($mediaId, $status, $conn) {
			$conn->query("DELETE FROM queue WHERE media_id=$mediaId");
			$conn->query("UPDATE media SET status=\"$status\" WHERE id=$mediaId");
			$this->fixQueueItemPositions($conn);
		}
		
		/**
		 * Checks if enough time has passed for the script to run again.
		 * Avoids having multiple run scripts be running at the same time.
		 */
		private function checkTimeDifference($lastRunTime) {
			return microtime(true) >= $lastRunTime + 1.99;
		}

		/**
		 * Checks if so much time has passed that there likely was an interruption & the processes should be restarted.
		 * Time is currently set to 1 hour because otherwise, we might have 2 c++ reognition proesses running.
		 * @return true if one hour has passed sine the last run time.
		 */
		private function wasProbablyInterrupted($lastRunTime) {
			return microtime(true) >= $lastRunTime + 3600;
		}

		private function readTime() {
			$timeFilePath = $this->getTimeFilePath();
			if(file_exists($timeFilePath)) {
				return floatval(file_get_contents($this->getTimeFilePath()));
			} else {
				return 0;
			}
		}

		private function writeTime() {
			$fh = fopen($this->getTimeFilePath(), "w+");
			fwrite($fh, microtime(true));
			fflush($fh);
			fclose($fh);
		}

		private function getTimeFilePath() {
			return $this->getAbsolutePathOnServer() . "/../video_downloads/runscript_time.txt";
		}

	}

	$mediaTextRecognitionLogic = new MediaTextRecognitionLogic();

	if(isset($_GET) && array_key_exists("reset_status", $_GET) && $_GET["reset_status"] == 1) {
		$conn = getDBConnection();
		$mediaTextRecognitionLogic->resetStatus($conn);
		$conn->close();
	}

	while(true) {
		if(get("queue_status") == "running") {
			if($mediaTextRecognitionLogic->run()) {
				sleep(2);
			} else {
				break;
			}
		} else {
			sleep(5);
		}
	}


?>