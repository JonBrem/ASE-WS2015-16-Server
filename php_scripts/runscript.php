<?php
	/* this should be running continuously. */
	require_once("dbConnection.php");

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
			/* @TODO */
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
			$sqlResult = $conn->query("SELECT `queue`.`media_id`, `media`.`video_url` FROM queue LEFT JOIN media ON `queue`.`media_id` = `media`.`id` WHERE (`position`<=3 AND `queue`.`status`='in_queue')");
			$this->createVideoDownloadsDirIfNotExists();		

			if($sqlResult->num_rows > 0) {
				while($row = $sqlResult->fetch_assoc()) {
					if($conn->query("UPDATE queue SET `status`=\"downloading\" WHERE `media_id` = $row[media_id]")) {


						$this->downloadInBackground($row["media_id"], $row["video_url"], "/opt/lampp/htdocs/ase_server/video_downloads/$row[media_id].mp4");
					}
				}
			}
		}

		private function downloadInBackground($mediaId, $video, $downloadTo) {
			$fileName = pathinfo(__FILE__, PATHINFO_FILENAME) . "php";

			$currentScriptOnServer = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$videoDonwloadScript = substr($currentScriptOnServer, 0, strlen($currentScriptOnServer) - strlen($fileName) - 1) . "video_download_script.php";
			curl_request_async($videoDonwloadScript, array(
					"video_url" => $video,
					"download_to" => $downloadTo,
					"item_id" => $mediaId
				), "GET");
		}


		private function readTime() {
			return 0;
		}

		private function writeTime() {

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
			$sqlResult = $conn->query("SELECT id FROM media WHERE (status='crawled') ORDER BY ID ASC LIMIT $howMany");
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

					if($conn->query("UPDATE media SET `status`='in_queue' WHERE (`id`=" . $items[$i]["media_id"] . ");") === TRUE) {
						$insertString .= $itemString;
					}
				}

				$insertString .= ";";

				$conn->query($insertString);
			}
		}

		/**
		 * Checks if enough time has passed for the script to run again.
		 * Avoids having multiple run scripts be running at the same time.
		 */
		private function checkTimeDifference($lastRunTime) {
			return true;
		}

		private function createVideoDownloadsDirIfNotExists() {
			$videoDownloadPath = realpath(dirname(__FILE__));
			$videoDownloadPath .= "/../video_downloads";

			if(!file_exists($videoDownloadPath)) {
				mkdir($videoDownloadPath, 0777);
			}

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