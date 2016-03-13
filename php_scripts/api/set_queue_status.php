<?php
	require_once('../set_config.php');

	function setQueueStatus($run) {
		if($run === "true" || $run === "1" || $run == "running") {
			set(CONFIG_QUEUE_STATUS, "running");
		} else if($run === "false" || $run === "0" || $run == "stop") {
			set(CONFIG_QUEUE_STATUS, "stop");
		} else {
			exit('{"status":"error", "message" : "invalid value"}');
		}

		echo '{"status":"ok"}';
	}

	setQueueStatus($_GET["queue_status"]);
