<?php

	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");

	/**
	 * Retrieves data for all the videos that caused errors while they were being processed.
	 * @return format: JSON array, items: {id: , assigned_id: , title: , url: , preview_img: , vieo_url: , status: }
	 */
	function get_erroneous_items() {
		$conn = getDBConnection();

		$sqlResult = $conn->query("SELECT * FROM media WHERE status IN (" . ERRORS_SQL . ")");

		if($sqlResult->num_rows > 0) {
			$erroneousItems = array();

			while($error = $sqlResult->fetch_assoc()) {

				$erroneousItems[] = array(
					"id" => $error["id"],
					"assigned_id" => $error["assigned_id"],
					"title" => utf8_encode($error["title"]),
					"url" => $error["url"],
					"preview_img" => $error["preview_image"],
					"video_url" => $error["video_url"],
					"status" => $error["status"]
				);
			}

			echo json_encode($erroneousItems);

		} else {
			echo "[]";
		}

		$conn->close();
	}

	get_erroneous_items();