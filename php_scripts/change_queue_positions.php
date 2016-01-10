<?php
	
	$newPositions = $_GET["positions"];
	$newPositions = json_decode($newPositions);

	$server = "localhost";
	$username = "root";
	$pw = "";
	$dbname = "ase_text_in_images";

	$conn = new mysqli($server, $username, $pw, $dbname);

	for($i = 0; $i < sizeof($newPositions); $i++) {
		$position = $newPositions[$i][1];
		$mediaId = $newPositions[$i][0];
		$conn->query("UPDATE queue SET position=$position WHERE media_id=$mediaId");
	}

	echo json_encode(array("status" => "OK"));

?>