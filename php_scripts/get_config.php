<?php

	require_once('util/config.php');

	/**
	 *  Retrieves the value of configuration parameters.
	 *
	 * <br>
	 * API-ish (but unlikely to be used outside of the UI).
	 * @param $which Key of the param (optional; if no value is given, all values will be returned).
	 */
	function get_config($which = null) {
		if($which != null) {
			echo json_encode(array(
				$which => get($which)
			));
		} else {
			echo json_encode(getMultiple(array(
				CONFIG_QUEUE_STATUS,
				CONFIG_FFPROBE_PATH,
				CONFIG_FFMPEG_PATH,
				CONFIG_EXE_PATH,
				CONFIG_JAVA_EVAL_PATH,
				CONFIG_RECOGNITION_SETTING
			)));
		}
	}

	if(isset($_GET) && isset($_GET['which'])) {
		get_config($_GET['which']);
	} else {
		get_config(null);
	}
	