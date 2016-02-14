<?php
	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");
	require_once("../util/config.php");
	require_once('../../vendor/autoload.php');

	$mediaID = $_GET['media_id'];
	$queueID = $_GET['queue_id'];
	$videoFilePath = $_GET['video_file_path'];
	$segmentedVideoPath = $_GET['segmented_video_path'];
	
	$conn = getDBConnection();

	if(!file_exists($videoFilePath)) {
		$conn->query("UPDATE queue SET status=\"" . STATUS_DOWNLOAD_ERROR . "\" WHERE id=$queueID");
		exit("Datei wurde nicht gefunden.");
	}

	// first: delete all files in $segmentedVideoPath
	$files = glob("$segmentedVideoPath/*"); // get all file names
	foreach($files as $file){ // iterate files
	 	if(is_file($file))
	    	unlink($file); // delete file
	}

	if(!file_exists($segmentedVideoPath)) {
		mkdir($segmentedVideoPath, 0777);
	}
	chmod($segmentedVideoPath, 0777);


	$conn->query("UPDATE queue SET status=\"" . STATUS_SEGMENTING_VIDEO . "\" WHERE id=$queueID");

	$ffprobe = null;
	$ffmpeg = null;

	try {
		$ffmpegBinariesPath = get(CONFIG_FFMPEG_PATH);
		$ffprobeBinariesPath = get(CONFIG_FFPROBE_PATH);

		$createArray = array();

		if($ffmpegBinariesPath != null && strlen($ffmpegBinariesPath) > 0) {
			$createArray["ffmpeg.binaries"] = $ffmpegBinariesPath;
		} 
		if($ffprobeBinariesPath != null && strlen($ffprobeBinariesPath) > 0) {
			$createArray["ffprobe.binaries"] = $ffprobeBinariesPath;
		}

		$ffmpeg = FFMpeg\FFMpeg::create($createArray);
		$ffprobe = FFMpeg\FFProbe::create($createArray);

	} catch (Exception $e) {
		$conn->query("UPDATE queue SET status=\"" . STATUS_SEGMENTING_ERROR . "\" WHERE id=$queueID");
		$conn->close();
		error_log("Error initializing ffmpeg and/or ffprobe.");
		exit("Error creating ffmpeg and/or ffprobe.");
	}

	$videoDuration = $ffprobe
	    ->format($videoFilePath) // extracts file informations
	    ->get('duration');             // returns the duration property
	$video = $ffmpeg->open($videoFilePath);

	// 0.2: analyze 5 FPS
	for($i = 0, $counter = 0; $i < $videoDuration; $i += 0.2, $counter++) {
		$video
	    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($i))
	    ->save($segmentedVideoPath . "/frame_$counter.jpg");
	}

	
	$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_SEGMENTING_VIDEO . "\" WHERE id=$queueID");
	$conn->close();

	unlink($_GET["video_file_path"]);
