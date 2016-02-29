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
	error_log("Set status to being processed!");

	// both $vars just used for exec
	$out;
	$return_var;

	exec("$execPath $folder $jsonOutputPath 35 0.00005 0.05 1.5 0.85 0.85 2>&1", $out, $return_var);
	error_log("Exe is finished!");

	// var_dump($out);

	if(file_exists($jsonOutputPath)) {	
		error_log("Status should be set to finished_processing");

		$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_PROCESSING . "\" WHERE media_id=$mediaID");
	} else {
		error_log("Status should be set to processing_error");
		
		$conn->query("UPDATE queue SET status=\"" . STATUS_PROCESSING_ERROR . "\" WHERE media_id=$mediaID");
	}
	error_log("SQL should be changed!!");

	$conn->close();
