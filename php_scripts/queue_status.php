<?php
	
	
	$server = "localhost";
	$username = "root";
	$pw = "";
	$dbname = "ase_text_in_images";

	$conn = new mysqli($server, $username, $pw, $dbname);

	$queryResults = $conn->query("SELECT media.id,media.title,media.url,media.preview_image,queue.status FROM queue LEFT JOIN media ON queue.media_id=media.id");

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

?>