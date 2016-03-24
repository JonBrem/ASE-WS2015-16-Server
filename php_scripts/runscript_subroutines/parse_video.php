<?php
	require_once('../util/db_connection.php');
	require_once('../util/status_codes.php');
	require_once('../util/config.php');


	$folder = $_GET['segmented_video_path'];
	$jsonOutputPath = $_GET['output_file'];
	$mediaID = $_GET['media_id'];
	$trainingFilesFolder = $_GET["training_files_folder"];


	$execPath = get("exe_path");
	if($execPath == null || strlen($execPath) == 0) {
		$execPath = $trainingFilesFolder . "ASE-WS2015-16";
	}

	/**
	 * !runscript subroutine!
	 * <br>
	 * Calls the C++ executable that will create a file with all the text recognitions for the image files in the folder.
	 * Takes a <em>long</em> time. Should definitely be called in the background.
	 * 
	 * @param $execPath path to the exe (file system, not server paths)
	 * @param $folder path to the folder containing the images
	 * @param trainingFilesFolder path to the folder containing the training files
	 * @param $jsonOutputPath path for the output file
	 * @param $mediaID id of the media item that is being processed
	 */
	function parseVideo($execPath, $folder, $trainingFilesFolder, $jsonOutputPath, $mediaID) {

		$conn = getDBConnection();
		$conn->query("UPDATE queue SET status=\"" . STATUS_BEING_PROCESSED . "\" WHERE media_id=$mediaID");

		// both $vars just used for exec
		$out;
		$return_var;

		$recognitionConfig = get("recognition_config");

		if($recognitionConfig == null || $recognitionConfig == "quality") {
			exec("$execPath $folder $jsonOutputPath $trainingFilesFolder 30 0.00005 0.02 2.0 0.9 0.85 2>&1", $out, $return_var);
		} else {
			exec("$execPath $folder $jsonOutputPath $trainingFilesFolder 45 0.00002 0.02 1.0 0.9 0.9 2>&1", $out, $return_var);			
		}
		
		// var_dump($out); 
		// if this line is uncommented, somehow the next parts may not get executed. no real reason why they wouldn't be, but that's how it is.
		
		if(file_exists($jsonOutputPath)) {	
			$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_PROCESSING . "\" WHERE media_id=$mediaID");
		} else {
			$conn->query("UPDATE queue SET status=\"" . STATUS_PROCESSING_ERROR . "\" WHERE media_id=$mediaID");
		}

		$conn->close();
	}

	parseVideo($execPath, $folder, $trainingFilesFolder, $jsonOutputPath, $mediaID);