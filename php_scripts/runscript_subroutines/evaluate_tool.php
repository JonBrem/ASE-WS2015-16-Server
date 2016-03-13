<?php

	function evaluateTool($javaExec, $inputFile, $outputFile, $javaToolFolder, $index) {

		$out;
		$return_var;
		exec("java -jar $javaExec $javaToolFolder $inputFile $outputFile $index 2&>1", $out, $return_var);
		var_dump($out);

	}

	$files = array("12", "13", "14", "15", "16", "18", "20", "21", "22", "24", "25", "26", "28", "29", "30");
	$javaExec = "/opt/lampp/htdocs/ase_server/java_tool/ASE-WS2015-16-WordValidator.jar";

	$javaToolFolder = "/opt/lampp/htdocs/ase_server/java_tool";

	foreach ($files as $file) {
		$inputFile = "/opt/lampp/htdocs/ase_server/video_downloads/output_$file.json";

		for($index = 0; $index < 4; $index++) {
			$outputFile = "/opt/lampp/htdocs/ase_server/video_downloads/java_eval_output_" . $file . "_$index.txt";
			evaluateTool($javaExec, $inputFile, $outputFile, $javaToolFolder, $index);
		}

	}
