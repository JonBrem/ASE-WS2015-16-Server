<?php

	require_once('util/config.php');

	if(isset($_GET) && isset($_GET['which'])) {
		echo json_encode(array(
			$_GET['which'] => get($_GET['which'])
		));

	} else {
		echo json_encode(getMultiple(array(
			CONFIG_QUEUE_STATUS,
			CONFIG_FFPROBE_PATH,
			CONFIG_FFMPEG_PATH,
			CONFIG_EXE_PATH,
			CONFIG_JAVA_EVAL_PATH
		)));
	}
