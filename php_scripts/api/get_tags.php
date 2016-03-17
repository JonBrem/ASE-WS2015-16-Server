<?php
	require_once("api.php");

	if(!isset($_GET["id_type"]) || !isset($_GET["id_value"])) {
		exit('{"status":"error","message":"id_type and id_value need to be set"}');
	}

	$idType = $_GET["id_type"];
	$idValue = $_GET["id_value"];
	$api = new TextRecognitionAPI();

	if(array_key_exists("with_id", $_GET) && $_GET["with_id"] == 1) {
		$api->get_tags($idType, $idValue, true);
	} else {
		$api->get_tags($idType, $idValue);
	}

