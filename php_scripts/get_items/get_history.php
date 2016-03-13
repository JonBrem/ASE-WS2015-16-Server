<?php
	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");

	$conn = getDBConnection();

	$sqlResult = $conn->query("SELECT * FROM media WHERE status=\"" . STATUS_HISTORY . "\" ORDER BY id DESC");

	// build media ID string for later, set up media items array
	// array indizes = media id (so we don't have so many loops later :) )

	$mediaIDs = "(";
	$mediaItems = array();

	if($sqlResult->num_rows > 0) {
		while($row = $sqlResult->fetch_assoc()) {
			$mediaIDs .= $row["id"] . ",";

			$mediaItems[$row["id"]] = array(
				"id" => $row["id"],
				"title" => utf8_encode($row["title"]),
				"url" => $row["url"],
				"preview_img" => $row["preview_image"],
				"tags" => array()
			);		
		}
	}
	if(mb_substr($mediaIDs, -1) == ",") {
		$mediaIDs = substr($mediaIDs, 0, strlen($mediaIDs) - 1); 
	}
	$mediaIDs .= ")";

	// query using the media ID string, assign tags to the media items

	if(sizeof($mediaItems) > 0) {
		$tags = $conn->query("SELECT * FROM tags WHERE media_id IN $mediaIDs");
		if($tags->num_rows > 0) {
			while($row = $tags->fetch_assoc()) {
				// append "object" to the tags array in the media item
				$mediaItems[$row["media_id"]]["tags"][] = array(
					"id" => $row["id"],
					"content" => utf8_encode($row["content"]),
					"accepted" => $row["accepted"]
				);	
			}
		}
	}

	$conn->close();

	$arrayToPrint = array();

	foreach($mediaItems as $key => $item) {
		$arrayToPrint[] = $item;
	}

	echo json_encode($arrayToPrint);
?>