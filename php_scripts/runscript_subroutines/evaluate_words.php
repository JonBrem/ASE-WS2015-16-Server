<?php

	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');

	$file = $_GET["json_file"];
	$mediaID = $_GET["media_id"];
	$outputFilePath = $_GET["output_file"];
	$javaExec = $_GET["java_exec_path"];
	$trainingFolder = $_GET["training_folder"];

	$conn = getDBConnection();
	$conn->query("UPDATE queue SET status=\"" . STATUS_EVALUATING_WORDS . "\" WHERE media_id=$mediaID");
	$conn->close();

	$out;
	$return_var;
	exec("java -jar $javaExec $file $outputFilePath $trainingFolder 2>&1", $out, $return_var);

	var_dump($out);

	$conn = getDBConnection();
	$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_EVALUATING_WORDS . "\" WHERE media_id=$mediaID");
	$conn->close();