<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");

	function add_to_queue($video_file_url, $video_id = null, $title = null, $url = null, $preview_image = null) {
		$conn = getDBConnection();

		$sqlColumns = "video_url";
		$sqlValues = "\"$video_file_url\"";

		if($video_id != null) {
			$sqlColumns .= ",assigned_id";	
			$sqlValues .= ",\"$video_id\"";			
		}
		if($title != null) {
			$sqlColumns .= ",title";	
			$sqlValues .= ",\"$title\"";			
		}
		if($url != null) {
			$sqlColumns .= ",url";	
			$sqlValues .= ",\"$url\"";			
		}
		if($preview_image != null) {
			$sqlColumns .= ",preview_image";	
			$sqlValues .= ",\"$preview_image\"";			
		}

		$sqlColumns .= ",status";
		$sqlValues .= ",\"" . STATUS_IN_QUEUE . "\"";

		try {
			$conn->query("INSERT INTO media ($sqlColumns) VALUES ($sqlValues)");
			$generatedId = $conn->insert_id;

			if($generatedId != 0) {

				$queueItem = $conn->query("SELECT * FROM queue WHERE 1 ORDER BY position DESC");

				if($queueItem->num_rows > 0) {
					$result = $queueItem->fetch_assoc();
					$position = intval($result["position"]) + 1;
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES ($generatedId,$position,\"" . STATUS_IN_QUEUE . "\")");
				} else {
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES ($generatedId,1,\"" . STATUS_IN_QUEUE . "\")");
				}

				echo '{"status":"ok"}';
			}

		} catch(Exception $e) {
			exit($e);
		}
		$conn->close();
	}

	$videoFileUrl = null;
	$assignedId = null;
	$title = null;
	$url = null;
	$previewImage = null;

	$params = $_GET;
	if(array_key_exists("video_file_url", $params)) {
		$videoFileUrl = $params["video_file_url"];
	} else {
		exit('{"status" : "error", "message" : "key \"video_file_url\" needs to point to the video .mp4 file"}');
	}

	if(array_key_exists("video_id", $params)) {
		$assignedId = $params["video_id"];
	}

	if(array_key_exists("title", $params)) {
		$title = $params["title"];
	}

	if(array_key_exists("url", $params)) {
		$url = $params["url"];
	}

	if(array_key_exists("preview_image", $params)) {
		$previewImage = $params["preview_image"];
	}

	add_to_queue($videoFileUrl, $assignedId, $title, $url, $previewImage);
