<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");
	require_once("api_helper.php");


	function get_status($id_type, $id_value) {
		$conn = getDBConnection();

		$results = loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			exit('{"status":"error","message":"invalid id_type"}');
		}

		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			echo '{"status" : "ok", "video_status" : "' . $video['status'] . '"}';

		} else {
			exit('{"status":"error","message":"found no video for given id_value"}');			
		}


		$conn->close();
	}

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	get_status($idType, $idValue);

