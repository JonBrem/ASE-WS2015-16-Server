<?php 
	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");

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
		downloadFile($_GET["video_url"], $_GET["download_to"]);

		$conn = getDBConnection();
		$conn->query("UPDATE queue SET status=\"" . STATUS_DOWNLOADED . "\" WHERE media_id=$_GET[item_id];");

		$conn->close();

	} catch (Exception $e) {
		// @TODO write that in the Database!!! Throw the video out or something!!
		error_log("Some exception occurred while downloading the video or updating the db after the download.");
	}
?>