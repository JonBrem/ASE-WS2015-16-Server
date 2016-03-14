<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");

	function try_video_again($id_type, $id_value) {
		$conn = getDBConnection();

		$results = loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}		
		if($results->num_rows <= 0) {
			$conn->close();
			exit('{"status":"error","message":"no video found for given id_value"}');
		}

		$video = $results->fetch_assoc();

		try {
			if($video["status"] == STATUS_EVALUATING_ERROR || $video["status"] == STATUS_SEGMENTING_ERROR ||
				$video["status"] == STATUS_PROCESSING_ERROR || $video["status"] == STATUS_DOWNLOAD_ERROR) {
				
				$queueItem = $conn->query("SELECT * FROM queue WHERE 1 ORDER BY position DESC");

				if($queueItem->num_rows > 0) {
					$queueResult = $queueItem->fetch_assoc();
					$position = intval($queueResult["position"]) + 1;
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES (" . $video["id"] . ",$position,\"" . STATUS_IN_QUEUE . "\")");
				} else {
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES (" . $video["id"] . ",1,\"" . STATUS_IN_QUEUE . "\")");
				}

				$conn->query("UPDATE media SET status=\"" . STATUS_IN_QUEUE ."\" WHERE id=" . $video["id"]);
				echo '{"status" : "ok"}';
				
			} else {
				$conn->close();
				exit('{"status":"error","message":"no video found for given id_value"}');
			}
		} catch(Exception $e) {
			$conn->close();
			exit($e);
		}
		$conn->close();
	}

	// retrieve params

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	try_video_again($idType, $idValue);
