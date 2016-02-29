<?php

	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');

	$inputFile = $_GET["json_file"];
	$mediaID = $_GET["media_id"];
	$outputFile = $_GET["output_file"];
	$javaExec = $_GET["java_exec_path"];
	$javaToolFolder = $_GET["java_tool_folder"];

	/**
	 * Selects the most common/probable words from the recognized text and
	 * proposes them as tags for the video file.
	 * 
	 * @param $javaExec path to the java executable
	 * @param $inputFile path to the file that the c++ executable created
	 * @param $outputFile path to the file that the java executable will create
	 * @param $javaToolFolder path to the folder the java executable is in 
	 * @param $mediaID the ID of the video item
	 */
	function evaluateWords($javaExec, $inputFile, $outputFile, $javaToolFolder, $mediaID) {
		$conn = getDBConnection();
		$conn->query("UPDATE queue SET status=\"" . STATUS_EVALUATING_WORDS . "\" WHERE media_id=$mediaID");
		$conn->close();

		$out;
		$return_var;
		exec("java -jar $javaExec $javaToolFolder $inputFile $outputFile 2>&1", $out, $return_var);
		// var_dump($out);

		$conn = getDBConnection();
		addTags($mediaID, $outputFile, $conn);

		if(file_exists($outputFile)) {
			$conn->query("UPDATE queue SET status=\"" . STATUS_READY_FOR_HISTORY . "\" WHERE media_id=$mediaID");
		} else {
			$conn->query("UPDATE queue SET status=\"" . STATUS_EVALUATING_ERROR . "\" WHERE media_id=$mediaID");
		}
		$conn->close();
	}

	/**
	 * adds up to 5 tags from the newly created file to the video.
	 * 
	 * @param $mediaID the ID of the video item
	 * @param tagsFile path to the file the java executable created
	 * @param $conn open SQL conneciton
	 */
	function addTags($mediaID, $tagsFile, $conn) {
		$handle = fopen($tagsFile, "r");
		$index = 0;
		if ($handle) {
		    while (($line = fgets($handle)) !== false) {
		    	$parts = explode("\t", $line);
		    	$tag = $parts[0];
				$conn->query("INSERT INTO tags (media_id,content,accepted) VALUES ($mediaID,\"$tag\",0)");
		    	$index++;
		    	if($index > 4) break;
		    }

		    fclose($handle);
		} else {
		    // error opening the file.
		} 
	}


	evaluateWords($javaExec, $inputFile, $outputFile, $javaToolFolder, $mediaID);