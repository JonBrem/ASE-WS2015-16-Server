<?php
	require_once("api.php");

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];

	$api = new TextRecognitionAPI();

	$api->delete_video($idType, $idValue);