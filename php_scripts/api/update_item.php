<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");

	function update_item($id_type, $id_value, $video_file_url, $video_id = null, $title = null, $url = null, $preview_image = null) {
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

		$sqlUpdate = "UPDATE media SET `video_url`=\"$video_file_url\",";

		if($video_id != null) {
			$sqlUpdate .= "`assigned_id`=\"$video_id\",";			
		} else {
			$sqlUpdate .= "`assigned_id`=null,";
		}

		if($title != null) {
			$sqlUpdate .= "`title`=\"$title\",";			
		} else {
			$sqlUpdate .= "`title`=null,";
		}

		if($url != null) {
			$sqlUpdate .= "`url`=\"$url\",";			
		} else {
			$sqlUpdate .= "`url`=null,";
		}

		if($preview_image != null) {
			$sqlUpdate .= "`preview_image`=\"$preview_image\"";			
		} else {
			$sqlUpdate .= "`preview_image`=null";
		}

		$sqlUpdate .= " WHERE id=" . $video["id"];

		try {
			if($video["status"] == "in_queue") {
				$videoInQueueResults = $conn->query("SELECT * FROM queue WHERE media_id=\"" . $video["id"] . "\"");
				if($videoInQueueResults->num_rows > 0) {
					$videoInQueue = $videoInQueueResults->fetch_assoc();

					$queueStatus = $videoInQueue["status"];

					if($queueStatus == STATUS_DOWNLOADING) {
						$conn->close();
						exit('{"status":"error","message":"cannnot change video while it is being downloaded"}');
					} else if ($queueStatus == STATUS_DOWNLOADED) {
						if($video["video_url"] != $video_file_url) {
							$conn->query("UPDATE queue SET status=\"" . STATUS_IN_QUEUE . "\" WHERE id=" . $videoInQueue["id"]);
							// no need to clean up (I think), video will be overwritten if the new url is working.
							// probably doesn't change, anyways...
						}
						$conn->query($sqlUpdate);
						echo '{"status":"ok"}';
					} else if ($queueStatus == STATUS_IN_QUEUE) {
						$conn->query($sqlUpdate);
						echo '{"status":"ok"}';
					} else { // could make more cases if the timing is just right but... no.
						$conn->close();
						exit('{"status":"error","message":"cannnot change video while it is being processed"}');
					}
				} else {
					$conn->close();
					exit('{"status":"error","message":"something went wrong, video should be in queue but could not be found there."}');
				}
			} else {
				$conn->query($sqlUpdate);
				echo '{"status":"ok"}';
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
		$title = utf8_decode(urldecode($params["title"]));
	}

	if(array_key_exists("url", $params)) {
		$url = $params["url"];
	}

	if(array_key_exists("preview_image", $params)) {
		$previewImage = $params["preview_image"];
	}

	update_item($idType, $idValue, $videoFileUrl, $assignedId, $title, $url, $previewImage);
