<?php
	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');


	// @todo: make SQL var that users can edit
	$execPath = "/home/jon/.CLion12/system/cmake/generated/82e833ec/82e833ec/Debug/ASE-WS2015-16";

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
	$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_PROCESSING . "\" WHERE media_id=$mediaID");
	$conn->close();
