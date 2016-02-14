<?php

	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");

	$conn = getDBConnection();

	$sqlResult = $conn->query("SELECT * FROM media WHERE status IN (" . ERRORS_SQL . ")");

	if($sqlResult->num_rows > 0) {
		$erroneousItems = array();

		while($error = $sqlResult->fetch_assoc()) {

			$erroneousItems[] = array(
				"id" => $error["id"],
				"title" => utf8_encode($error["title"]),
				"url" => $error["url"],
				"preview_img" => $error["preview_image"],
				"status" => $error["status"]
			);
		}

		echo json_encode($erroneousItems);

	} else {
		echo "[]";
	}

	$conn->close();
