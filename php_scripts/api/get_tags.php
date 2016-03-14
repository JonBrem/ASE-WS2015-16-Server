<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");

	function get_tags($id_type, $id_value, $with_id = false) {
		$conn = getDBConnection();

		$results = loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}

		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			if($video["status"] == STATUS_HISTORY) {
				$tagsResults = $conn->query("SELECT * FROM tags WHERE media_id=\"" . $video["id"] . "\"");

				$acceptedTagsArray = array();
				$tagsArray = array();

				if($tagsResults->num_rows > 0) {
					while($tagResult = $tagsResults->fetch_assoc()) {
						if($with_id) {
							$tagsArray[] = array("id" => $tagResult["id"], "value" => $tagResult["content"], "accepted" => $tagResult["accepted"]);
						} else {
							$tagsArray[] = $tagResult["content"];
							if($tagResult["accepted"]) {
								$acceptedTagsArray[] = $tagResult["content"];
							}
						}
					}
				}

				if($with_id) {
					echo json_encode(array('status' => "ok", 'tags' => $tagsArray));
				} else {
					echo json_encode(array('status' => "ok", 'tags' => $tagsArray, 'accepted_tags' => $acceptedTagsArray));
				}
			} else {
				echo '{"status":"error","message":"video processing is not finished"}';			
			}

		} else {
			echo '{"status":"error","message":"found no video for given id_value"}';			
		}


		$conn->close();
	}

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	if(array_key_exists("with_id", $_GET) && $_GET["with_id"] == 1) {
		get_tags($idType, $idValue, true);
	} else {
		get_tags($idType, $idValue);
	}

