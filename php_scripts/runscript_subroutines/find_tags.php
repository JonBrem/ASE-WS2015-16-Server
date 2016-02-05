<?php

	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');

	$fileName = $_GET["file_name"];
	$mediaID = $_GET["media_id"];

	$fileContents = file_get_contents($fileName);

	// $conn = getDBConnection();
	// $conn->query("UPDATE queue SET status=\"" . STATUS_LOOKING_FOR_TAGS . "\"	 WHERE media_id=$mediaID");
	// $conn->close();

	$lines = split("\n", $fileContents);

	foreach($lines as $line) {
		// $word = substr($line, 0, strrpos($line, "\t"));

		// $url = "https://www.openthesaurus.de/synonyme/search?q=$word&format=application/json&similar=true";

		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_HEADER, 0);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// $answer = curl_exec($ch);

		// echo "<pre>";
		// echo $answer;
		// echo "</pre>";

		// curl_close($ch);
		// break;
	}
	
	// $conn = getDBConnection();
	// $conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_PROCESSING . "\" WHERE media_id=$mediaID");
	// $conn->close();
