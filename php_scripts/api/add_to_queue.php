<?php
	require_once("api.php");

	$videoFileUrl = null;
	$assignedId = null;
	$title = null;
	$url = null;
	$previewImage = null;

	$params = $_GET;
	if(array_key_exists("video_file_url", $params)) {
		$videoFileUrl = $params["video_file_url"];
	} else {
		exit('{"status" : "error", "message" : "key \"video_file_url\" needs to point to the video .mp4 file"}');
	}

	if(array_key_exists("video_id", $params)) {
		$assignedId = $params["video_id"];
	}

	if(array_key_exists("title", $params)) {
		$title = $params["title"];
	}

	if(array_key_exists("url", $params)) {
		$url = $params["url"];
	}

	if(array_key_exists("preview_image", $params)) {
		$previewImage = $params["preview_image"];
	}

	$api = new TextRecognitionAPI();
	$api->add_to_queue($videoFileUrl, $assignedId, $title, $url, $previewImage);
