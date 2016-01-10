<?php
	require_once("dbConnection.php");

	$newPositions = $_GET["positions"];
	$newPositions = json_decode($newPositions);

	$conn = getDBConnection();

	for($i = 0; $i < sizeof($newPositions); $i++) {
		$position = $newPositions[$i][1];
		$mediaId = $newPositions[$i][0];
		$conn->query("UPDATE queue SET position=$position WHERE (media_id=$mediaId AND status!=\"being_processed\")");
	}

	echo json_encode(array("status" => "OK"));
	$conn->close();
?>