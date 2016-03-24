<?php
	require_once("api.php");


	// retrieve params

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	$videoFileUrl = null;
	$assignedId = null;
	$title = null;
	$url = null;
	$previewImage = null;

	$params = $_GET;
	if(array_key_exists("video_file_url", $params)) {
		$videoFileUrl = $params["video_file_url"];
	} else {
		exit('{"status" : "error", "message" : "key \"video_file_url\" needs to point to the video file"}');
	}

	if(array_key_exists("video_id", $params)) {
		$assignedId = $params["video_id"];
	}

	if(array_key_exists("title", $params)) {
		$title = utf8_decode(urldecode($params["title"]));
	}

	if(array_key_exists("url", $params)) {
		$url = $params["url"];
	}

	if(array_key_exists("preview_image", $params)) {
		$previewImage = $params["preview_image"];
	}

	// call api

	$api = new TextRecognitionAPI();
	$api->update_item($idType, $idValue, $videoFileUrl, $assignedId, $title, $url, $previewImage);
