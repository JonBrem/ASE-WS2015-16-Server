<?php
	require_once("util/db_connection.php");
	require_once("util/status_codes.php");

	/*
	 * needs ?positions=... to be set in a two-dimensional JSON-format:
	 * [[VIDEO_ID,POSITION],[VIDEO_ID,POSITION],...] and so on
	 */


	$newPositions = $_GET["positions"];
	$newPositions = json_decode($newPositions);

	$conn = getDBConnection();

	for($i = 0; $i < sizeof($newPositions); $i++) {
		$position = $newPositions[$i][1];
		$mediaId = $newPositions[$i][0];
		$conn->query("UPDATE queue SET position=$position WHERE (media_id=$mediaId AND status!=\"" . STATUS_BEING_PROCESSED . "\")");
	}

	echo json_encode(array("status" => "OK"));
	$conn->close();
?>