<?php

	require_once('util/config.php');

	/**
	 * Call to {@link set}, changes the value of a settings item.
	 * Can NOT add a new configuration item! That has to happen in the DB. This method only changes values.
	 * <br>
	 * API-ish (but unlikely to be used outside of the UI).
	 * @param $which name / key of the setting
	 * @param $val new value of the settings item
	 */
	function set_config($which, $val) {
		set($_GET["which"], $_GET["val"]);
	}

	if(isset($_GET["which"]) && isset($_GET["val"])) {
		set_config($_GET["which"], $_GET["val"]);
	}
