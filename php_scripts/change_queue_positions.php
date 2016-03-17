<?php
	require_once("util/db_connection.php");
	require_once("util/status_codes.php");

	/**
	 * Updates the positions / order of items in the queue.
	 * The parameter needs to be an array in a two-dimensional format:
	 * [[VIDEO_ID,POSITION],[VIDEO_ID,POSITION],...] and so on and sent to the script in a JSON format.
	 */
	function change_queue_positions($positions) {
		$conn = getDBConnection();

		for($i = 0; $i < sizeof($positions); $i++) {
			$position = $positions[$i][1];
			$mediaId = $positions[$i][0];
			$conn->query("UPDATE queue SET position=$position WHERE (media_id=$mediaId AND status!=\"" . STATUS_BEING_PROCESSED . "\")");
		}

		echo json_encode(array("status" => "ok"));
		$conn->close();

	}

	if(!array_key_exists("positions", $_GET)) {
		exit('{"status" : "error", "message": "positions need to be sent (as a two-dimensional array)"}');
	}

	$newPositions = $_GET["positions"];
	$newPositions = json_decode($newPositions);
	change_queue_positions($newPositions);
?>