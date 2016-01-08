<?php
	
	// TODO: CREATE THE FOLLOWING ARRAY (From SQL, Files, whatever)!!!

	$items = array(
		0 => array(
			"id" => 1,
			"title" => "Glashüttenbrettl: Bandporträt „JanaJa“ (Viechtach)",
			"url" => "http://lokalreporter.idvl.de/mediathek/page/52/video/glashuettenbrettl-bandportraet-janaja-viechtach/",
			"preview_img" => "http://lokalreporter.idvl.de/storage/thumbs/1200x630c/r:1444294864/345.jpg",
			"status" => "being_processed",
			"progress" => 0.25
			),
		1 => array(
			"id" => 2,
			"title" => "Glashüttenbrettl: Bandporträt „JanaJa“ (Viechtach)",
			"url" => "http://lokalreporter.idvl.de/mediathek/page/52/video/glashuettenbrettl-bandportraet-janaja-viechtach/",
			"preview_img" => "http://lokalreporter.idvl.de/storage/thumbs/1200x630c/r:1444294864/345.jpg",
			"status" => "being_downloaded",
			),
		2 => array(
			"id" => 3,
			"title" => "Glashüttenbrettl: Bandporträt „JanaJa“ (Viechtach)",
			"url" => "http://lokalreporter.idvl.de/mediathek/page/52/video/glashuettenbrettl-bandportraet-janaja-viechtach/",
			"preview_img" => "http://lokalreporter.idvl.de/storage/thumbs/1200x630c/r:1444294864/345.jpg",
			"status" => "in_queue"
			),
		3 => array(
			"id" => 4,
			"title" => "Glashüttenbrettl: Bandporträt „JanaJa“ (Viechtach)",
			"url" => "http://lokalreporter.idvl.de/mediathek/page/52/video/glashuettenbrettl-bandportraet-janaja-viechtach/",
			"preview_img" => "http://lokalreporter.idvl.de/storage/thumbs/1200x630c/r:1444294864/345.jpg",
			"status" => "in_queue"
			)
		);

	echo json_encode($items);

?>