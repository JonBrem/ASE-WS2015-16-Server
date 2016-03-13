<?php
	require_once("../util/config.php");
	require_once("../util/status_codes.php");
	require_once("../util/db_connection.php");

	/**
	 * decomposition method that allows multiple types of queries
	 * (we cannot predict how exactly the software will be used at this point, so being flexible won't hurt).
	 * 
	 * @param $id_type possible values: video_id, db_id or video_file_url; will return null otherwise
	 * @param $id_value value for the specified video and id_type
	 * @param $conn open SQL connection
	 */
	function loadForIdTypeAndIdValue($id_type, $id_value, $conn) {
		$results = null;

		// LIBRARY ODER UTIL ODER SO FÜR DAS HIER!!
		if($id_type == "video_id") {
			$results = $conn->query("SELECT * FROM media WHERE assigned_id=\"$id_value\"");
		} else if ($id_type == "db_id") {
			$results = $conn->query("SELECT * FROM media WHERE id=\"$id_value\"");
		} else if ($id_type == "video_file_url") {
			$results = $conn->query("SELECT * FROM media WHERE video_url=\"$id_value\"");
		}

		return $results;
	}
