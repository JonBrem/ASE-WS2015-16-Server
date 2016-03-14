<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");

	function get_tags($id_type, $id_value) {
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

				$tagsArray = array();

				if($tagsResults->num_rows > 0) {
					while($tagResult = $tagsResults->fetch_assoc()) {
						$tagsArray[] = $tagResult["content"];
					}
				}

				echo json_encode(array('status' => "ok", 'tags' => $tagsArray));

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

	get_tags($idType, $idValue);
