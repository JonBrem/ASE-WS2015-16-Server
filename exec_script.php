<?php

	$out;
	$return_var;

	// get params
	$execPath = $_GET["exec_path"];
	$img = $_GET["img"];
	$toFile = $_GET["toFile"];

	// execute program
	exec("$execPath $img $toFile",
		$out, $return_var);

	// return in json format
	echo "[";

	if($return_var == 0) {
		for($i = 0; $i < sizeof($out); $i++) {
			echo '"' . $out[$i] . '"';
			if($i != sizeof($out) - 1) echo ",";
		}
	}
	echo "]";
?>