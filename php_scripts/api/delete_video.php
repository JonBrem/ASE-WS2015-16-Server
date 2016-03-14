<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");


	function get_status($id_type, $id_value) {
		$conn = getDBConnection();

		$results = loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}


		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			if($video["status"] == STATUS_IN_QUEUE) {
				$videoInQueue = $conn->query("SELECT * FROM queue WHERE media_id=\"" . $video["id"] . "\"");

				if($videoInQueue->num_rows > 0) {
					$assocRow = $videoInQueue->fetch_assoc();
					if($assocRow["status"] == STATUS_DOWNLOADED) {
						deleteVideoFile($video["id"]);
					}

					if ($assocRow["status"] == STATUS_DOWNLOADING) {
						$conn->close();
						exit('{"status":"error","message":"cannot delete video while it is being downloaded"}'); // mostly for our convenience...
					} else if(!in_array($assocRow["status"], array(
						STATUS_DOWNLOADED, STATUS_IN_QUEUE, STATUS_EVALUATING_ERROR, STATUS_SEGMENTING_ERROR,
						STATUS_PROCESSING_ERROR, STATUS_DOWNLOAD_ERROR
					))) {
						$conn->close();
						exit('{"status":"error","message":"cannot delete video while it is being processed"}'); // mostly for our convenience...
					}
				}

				$conn->query("DELETE FROM queue WHERE media_id=\"" . $video["id"] . "\"");
				$conn->query("DELETE FROM media WHERE id=\"" . $video["id"] . "\"");
				// maybe fix positions (but that happens in runscript anyway...)
			} else if ($video["status"] == STATUS_HISTORY ) {
				$conn->query("DELETE FROM tags WHERE media_id=" . $video["id"]);
				$conn->query("DELETE FROM media WHERE id=\"" . $video["id"] . "\"");
			} else {
				$conn->query("DELETE FROM media WHERE id=\"" . $video["id"] . "\"");
			}

			echo '{"status":"ok"}';

		} else {
			echo '{"status":"error","message":"found no video for given id_value"}';			
		}


		$conn->close();
	}

	function deleteVideoFile($id) {
		$currentPath = realpath(dirname(__FILE__));

		$videoPath = substr($currentPath, 0, strrpos($currentPath, "/"));
		$videoPath = substr($videoPath, 0, strrpos($videoPath, "/"));

		$videoPath .= '/video_downloads/' . $id . ".mp4";

		try {
			unlink($videoPath);
		} catch (Exception $ignored) {

		}
	}

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	get_status($idType, $idValue);