<?php

	/**
	 * Prints "never" or the epoch of the last execution of the main routine (in the runscript_time.txt file)
	 */
	function get_last_execution_time() {
		$filePath = realpath(dirname(__FILE__)) . "/../video_downloads/runscript_time.txt";

		if(file_exists($filePath)) {
			$lastExeutionTime =  file_get_contents($filePath);
			$lastExecutionTimeStamp = round($lastExeutionTime * 1000);
			echo $lastExecutionTimeStamp;
		} else {
			echo "never";
		}
	}

	get_last_execution_time();