<?php
	require_once("../util/db_connection.php");

	/**
	 * Retrieves data for all the videos in the queue from the database.
	 * @return format: JSON array, items: {id: , assigned_id: , title: , url: , preview_img: , vieo_url: , status: , position: }
	 */
	function get_queue() {
		$conn = getDBConnection();

		$queryResults = $conn->query("SELECT media.id,media.assigned_id,media.title,media.url,media.preview_image,media.video_url,queue.status,queue.position FROM queue LEFT JOIN media ON queue.media_id=media.id ORDER BY queue.position");

		$queue = array();

		if($queryResults->num_rows > 0) {
			while($row = $queryResults->fetch_assoc()) {
				$queue[] = array(
					"id" => $row["id"],
					"assigned_id" => $row["assigned_id"],
					"title" => utf8_encode($row["title"]),
					"url" => $row["url"],
					"preview_img" => $row["preview_image"],
					"video_url" => $row["video_url"],
					"status" => $row["status"],
					"position" => $row["position"]
				);
			}
		}

		echo json_encode($queue);
		$conn->close();
	}

	get_queue();
?>