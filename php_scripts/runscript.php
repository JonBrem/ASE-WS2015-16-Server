<?php
	/* this should be running continuously. */
	require_once("util/db_connection.php");
	require_once("util/status_codes.php");

	class MediaTextRecognitionLogic {

		public function run() {
			$lastRunTime = $this->readTime();
			if($this->checkTimeDifference($lastRunTime)) {
				$this->writeTime();

				$conn = getDBConnection();
				if(!$conn->connect_error) {
					$this->checkCurrentTextRecognition($conn);
					$this->checkQueue($conn);
					$this->checkQueueDownloads($conn);
				} else {
					error_log("runscript in media text recognition can't connect to database.");
				}

				$conn->close();
				// sleep(2);
				// $this->run();
			} else { # not enough time has passed for this to run again.
				// do nothing, let this end.
			}
		}

		/**
		 * If a text recognition is in progress, this does nothing.
		 * If there is not, this sees whether or not it just finished and 
		 * whether or not a new text recognition needs to be started.
		 */
		private function checkCurrentTextRecognition($conn) {
			// todo make this more of an "if this then that"-pipeline
			$this->checkIfSegmentingVideoFinished($conn);
			$this->checkIfParsingFinished($conn);
			$this->checkIfFindingTagsFinished($conn);

			if(!$this->currentlyProcessingImage($conn)) {
				$item = $this->getItemToProcess($conn);
				if($item !== false) {
					$this->segmentVideo($item, $conn);
				}
			}
		}

		private function checkIfParsingFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_FINISHED_PROCESSING ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->findAndStoreTagsFromRecognizedText($row['media_id'], $conn);
			}
		}

		private function checkIfSegmentingVideoFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_FINISHED_SEGMENTING_VIDEO ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->parseVideo($row['media_id'], $conn);
			}
		}

		private function checkIfFindingTagsFinished($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id` FROM queue WHERE `status`=\"". STATUS_READY_FOR_HISTORY ."\"");
			if($sqlResult->num_rows > 0) {
				$row = $sqlResult->fetch_assoc();
				$this->moveToHistory($row['media_id'], $conn);
			}
		}

		private function findAndStoreTagsFromRecognizedText($mediaID, $conn) {
			
		}

		private function parseVideo($mediaID, $conn) {
			$parseVideoScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/parse_video.php";
			$outputFilePath = $this->getAbsolutePathOnServer() . "/../video_downloads/output_$mediaID.json";
			$videosFolder = $this->getAbsolutePathOnServer() . "/../video_downloads";
			$segmentedVideoPath = "$videosFolder/video_segments";

			curl_request_async(
				$parseVideoScript, 
				array(
					"segmented_video_path" => $segmentedVideoPath,
					"output_file" => $outputFilePath,
					"media_id" => $mediaID 
					), 
				"GET");
		}

		private function moveToHistory($mediaID, $conn) {
			// @todo
		}

		private function currentlyProcessingImage($conn) {
			$condition = " `queue`.`status`=\"" . STATUS_BEING_PROCESSED . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_FINISHED_PROCESSING . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_LOOKING_FOR_TAGS . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_SEGMENTING_VIDEO . "\" OR"
						 . " `queue`.`status`=\"" . STATUS_FINISHED_SEGMENTING_VIDEO . "\"";

			$sqlResult = $conn->query("SELECT `queue`.`id` FROM queue WHERE $condition");

			return ($sqlResult->num_rows > 0);
		}

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
		 * @param $item
		 *		php array; must have keys "media_id" and "queue_id".
		 */
		private function segmentVideo($item) {
			$segmentVideoScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/segment_video.php";
			
			$videosFolder = $this->getAbsolutePathOnServer() . "/../video_downloads";

			$videoFilePath = "$videosFolder/$item[media_id].mp4"; // @todo this should probably be stored in sql & loaded from there
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
		 * 
		 *
		 */
		private function checkQueue($conn) {
			$numOfItemsInQueue = $this->getNumOfItemsInQueue($conn);
			if($numOfItemsInQueue < 10) {
				$items = $this->getNextItemsForQueue($conn, 10 - $numOfItemsInQueue, $numOfItemsInQueue);
				if(sizeof($items) > 0) {
					$this->appendItemsToQueue($conn, $items);
				}
			} 
		}

		private function checkQueueDownloads($conn) {
			$sqlResult = $conn->query("SELECT `queue`.`media_id`, `media`.`video_url` FROM queue LEFT JOIN media ON `queue`.`media_id` = `media`.`id` WHERE (`position`<=3 AND `queue`.`status`='". STATUS_IN_QUEUE ."')");
			$this->createVideoDownloadsDirIfNotExists();		

			if($sqlResult->num_rows > 0) {
				while($row = $sqlResult->fetch_assoc()) {
					if($conn->query("UPDATE queue SET `status`=\"". STATUS_DOWNLOADING ."\" WHERE `media_id` = $row[media_id]")) {

						// @todo update path!!!
						$this->downloadInBackground($row["media_id"], $row["video_url"], $this->getAbsolutePathOnServer() . "/../video_downloads/$row[media_id].mp4");
					}
				}
			}
		}

		private function downloadInBackground($mediaId, $video, $downloadTo) {
			$videoDonwloadScript = $this->getPhpScriptsPathOnServer() . "runscript_subroutines/video_download.php";
			curl_request_async($videoDonwloadScript, array(
					"video_url" => $video,
					"download_to" => $downloadTo,
					"item_id" => $mediaId
				), "GET");
		}

		private function getPhpScriptsPathOnServer() {
			$fileName = pathinfo(__FILE__, PATHINFO_FILENAME) . "php";
			$currentScriptOnServer = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			return substr($currentScriptOnServer, 0, strlen($currentScriptOnServer) - strlen($fileName) - 1);
		}

		private function getAbsolutePathOnServer() {
			return realpath(dirname(__FILE__));
		}

		private function getNumOfItemsInQueue($conn) {
			$result = $conn->query("SELECT COUNT(*) FROM queue");
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				return $row["COUNT(*)"];
			} else {
				return 0; // @TODO: maybe throw error?
			}
		}

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


		private function createVideoDownloadsDirIfNotExists() {
			$videoDownloadPath = realpath(dirname(__FILE__));
			$videoDownloadPath .= "/../video_downloads";

			if(!file_exists($videoDownloadPath)) {
				mkdir($videoDownloadPath, 0777);
			}

		}


		// @TODO
		private function readTime() {
			return 0;
		}

		private function writeTime() {

		}
		
		/**
		 * Checks if enough time has passed for the script to run again.
		 * Avoids having multiple run scripts be running at the same time.
		 */
		private function checkTimeDifference($lastRunTime) {
			return true;
		}

	}

	$mediaTextRecognitionLogic = new MediaTextRecognitionLogic();

	$mediaTextRecognitionLogic->run();

  // COPIED FROM: http://stackoverflow.com/questions/962915/how-do-i-make-an-asynchronous-get-request-in-php
  // $type must equal 'GET' or 'POST'
  function curl_request_async($url, $params, $type='POST') {
      foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
      }
      if(sizeof($params) == 0) $post_params = array();
      $post_string = implode('&', $post_params);

      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }
?>