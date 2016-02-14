<?php
	require_once('db_connection.php');

	define("CONFIG_QUEUE_STATUS", "queue_status");
	define("CONFIG_EXE_PATH", "exe_path");
	define("CONFIG_FFMPEG_PATH", "ffmpeg_path");
	define("CONFIG_FFPROBE_PATH", "ffprobe_path");
	define("CONFIG_JAVA_EVAL_PATH", "java_eval_path");

	/**
	 * Update a row in the configuration table to the value.
	 * @param $which one of the constants defined in the file (e.g. ffmpeg binaries path: ffmpeg_path)
	 * @param $val the new value of this constant
	 */ 
	function set($which, $val) {
		$conn = getDBConnection();

		if($which == CONFIG_QUEUE_STATUS) {
			if($val == "running" || $val == "stop") { // other values are not permitted
				$conn->query("UPDATE config SET value=\"$val\" WHERE name=\"" . CONFIG_QUEUE_STATUS . "\"");
			} else {
				$conn->close();
				return -1; 
			}
		} else {
			$conn->query("UPDATE config SET value=\"$val\" WHERE name=\"$which\"");

		}

		$conn->close();
	}

	/**
	 * Retrieve the specified value from the configuration table.
	 * @param $which one of the constants defined in the file (e.g. ffmpeg binaries path: ffmpeg_path)
	 * @return null (if no value was found for that "$which") or the value.
	 */
	function get($which) {
		$conn = getDBConnection();

		$results = $conn->query("SELECT * FROM config WHERE name=\"$which\"");
		if($results->num_rows > 0) {
			$row = $results->fetch_assoc();
			$conn->close();
			return $row['value'];
		} else {
			$conn->close();
			return null;
		}
	}

	/**
	 * Retrieve the specified values from the configuration table.
	 * @param $whichOnes array of some of the constants defined in the file (e.g. ffmpeg binaries path: ffmpeg_path)
	 * @return array (may be empty) containing the names of $whichOnes as keys (if it found the corresponding value) and the corresponding values as values.
	 */
	function getMultiple($whichOnes) {
		$sql = "SELECT * FROM config WHERE name IN (";
		for($i = 0; $i < sizeof($whichOnes); $i++) {
			$sql .= "\"" . $whichOnes[$i] . "\"";
			if($i != sizeof($whichOnes) - 1) $sql .= ",";
		}
		$sql .= ")";

		$results = array();

		$conn = getDBConnection();

		$sqlResult = $conn->query($sql);
		if($sqlResult->num_rows > 0) {
			while($row = $sqlResult->fetch_assoc()) {
				$results[$row['name']] = $row['value'];
			}
		}

		$conn->close();

		return $results;
	}

