<?php
	require_once("../util/db_connection.php");

	$conn = getDBConnection();

	$queryResults = $conn->query("SELECT media.id,media.title,media.url,media.preview_image,queue.status FROM queue LEFT JOIN media ON queue.media_id=media.id ORDER BY queue.position");

	$queue = array();

	if($queryResults->num_rows > 0) {
		while($row = $queryResults->fetch_assoc()) {
			$queue[] = array(
				"id" => $row["id"],
				"title" => utf8_encode($row["title"]),
				"url" => $row["url"],
				"preview_img" => $row["preview_image"],
				"status" => $row["status"]
			);
		}
	}

	echo json_encode($queue);
	$conn->close();
?>