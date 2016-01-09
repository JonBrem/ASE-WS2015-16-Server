<?php 

	function downloadFile($url, $path) {
	    $newfname = $path;
	    $file = fopen ($url, 'rb');
	    if ($file) {
	        $newf = fopen ($newfname, 'wb');
	        if ($newf) {
	            while(!feof($file)) {
	                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
	            }
	        }
	    }
	    if ($file) {
	        fclose($file);
	    }
	    if ($newf) {
	        fclose($newf);
	    }
	}

	try {
		error_log("Pre-Download!");

		downloadFile($_GET["video_url"], $_GET["download_to"]);

		error_log("POST-DOWNLOAD!!!");

		$server = "localhost";
		$username = "root";
		$pw = "";
		$dbname = "ase_text_in_images";

		$conn = new mysqli($server, $username, $pw, $dbname);

		$conn->query("UPDATE queue SET status=\"downloaded\" WHERE media_id=$_GET[item_id];");

		$conn->close();

	} catch (Exception $e) {
		error_log("Some exception occurred while downloading the video or updating the db after the download.");
	}
?>