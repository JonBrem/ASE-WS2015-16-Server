<?php
	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");
	require_once('../../vendor/autoload.php');

	$mediaID = $_GET['media_id'];
	$queueID = $_GET['queue_id'];
	$videoFilePath = $_GET['video_file_path'];
	$segmentedVideoPath = $_GET['segmented_video_path'];

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

	$conn = getDBConnection();

	$conn->query("UPDATE queue SET status=\"" . STATUS_SEGMENTING_VIDEO . "\" WHERE id=$queueID");

	
	$ffprobe = FFMpeg\FFProbe::create();
	$videoDuration = $ffprobe
	    ->format($videoFilePath) // extracts file informations
	    ->get('duration');             // returns the duration property


	$ffmpeg = FFMpeg\FFMpeg::create();
	$video = $ffmpeg->open($videoFilePath);

	// 0.2: analyze 5 FPS
	for($i = 0, $counter = 0; $i < $videoDuration; $i += 0.2, $counter++) {
		$video
	    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($i))
	    ->save($segmentedVideoPath . "/frame_$counter.jpg");
	}

	
	$conn->query("UPDATE queue SET status=\"" . STATUS_FINISHED_SEGMENTING_VIDEO . "\" WHERE id=$queueID");

	$conn->close();
