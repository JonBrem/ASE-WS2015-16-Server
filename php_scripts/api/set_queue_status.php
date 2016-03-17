<?php

	if(!array_key_exists("queue_status", $_GET)) {
		exit('{"status" : "error", "message" : "queue_status needs to be set"}');
	}

	$api = new TextRecognitionAPI();
	$api->set_queue_status($_GET["queue_status"]);
