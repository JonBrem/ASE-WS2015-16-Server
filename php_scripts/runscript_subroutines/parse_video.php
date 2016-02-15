<?php
	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');
	require_once('../util/config.php');


	// @todo: make SQL var that users can edit
	$execPath = get("exe_path");

	$folder = $_GET['segmented_video_path'];
	$jsonOutputPath = $_GET['output_file'];

	$mediaID = $_GET['media_id'];

	$conn = getDBConnection();
	$conn->query("UPDATE queue SET status=\"" . STATUS_BEING_PROCESSED . "\" WHERE media_id=$mediaID");
	$conn->close();

	// both $vars just used for exec
	$out;
	$return_var;

	exec("$execPath $folder $jsonOutputPath 2>&1", $out, $return_var);

	var_dump($out);


	$conn = getDBConnection();

	if(file_exists($jsonOutputPath)) {
		$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_PROCESSING . "\" WHERE media_id=$mediaID");
	} else {
		$conn->query("UPDATE queue SET status=\"" . STATUS_PROCESSING_ERROR . "\" WHERE media_id=$mediaID");
	}

	$conn->close();
