<?php

	require_once('util/config.php');

	if(isset($_GET["which"]) && isset($_GET["val"])) {
		set($_GET["which"], $_GET["val"]);
	}
