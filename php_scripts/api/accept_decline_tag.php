<?php
	require_once("api.php");

	if(array_key_exists("id", $_GET) && array_key_exists("accepted", $_GET)) {
		$api = new TextRecognitionAPI();
		$api->accept_decline_tag($_GET["id"], $_GET["accepted"]);
	} else {
		exit('{"status":"error", "message":"parameters id and accepted need to be set."}');
	}

?>