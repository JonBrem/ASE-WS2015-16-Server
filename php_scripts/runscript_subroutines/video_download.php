<?php 
	require_once("../util/db_connection.php");
	require_once("../util/status_codes.php");

	/**
	 * !runscript subroutine!
	 * <br>
 	 * downloads the file at the given url to the specified path.
 	 * 
	 * @param $url File to download; needs 'rb' permission.
	 * @param $path Where to download to; needs 'wb' permission.
	 */
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

		if(file_exists($_GET["download_to"])) {
			$status = STATUS_DOWNLOADED;
			chmod($_GET["download_to"], 0777);
		} else {
			// not within "catch"; downloadFile does not throw an error if the file can't be found.
			$status = STATUS_DOWNLOAD_ERROR;
		}

		$conn->query("UPDATE queue SET status=\"" . $status . "\" WHERE media_id=$_GET[item_id];");
		$conn->close();

	} catch (Exception $e) {
		$conn = getDBConnection();
		$conn->query("UPDATE queue SET status=\"" . STATUS_DOWNLOAD_ERROR . "\" WHERE media_id=$_GET[item_id];");
		$conn->close();
	}
?>