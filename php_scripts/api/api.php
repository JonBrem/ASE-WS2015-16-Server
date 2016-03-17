<?php

require_once("../util/config.php");
require_once("../util/status_codes.php");
require_once("../util/db_connection.php");

require_once('../set_config.php');

/**
 * API class, container for all the API methods.
 * <br>
 * The API methods don't have return values but instead print their results to the client.
 */
class TextRecognitionAPI {

	/**
	 * Accept (or undo the acceptance) of a tag with with method.
	 * 
	 * @param $id tag id (integer), retrieved from DB or with the get_tags API method (with with_id set to 1)
	 * @param $accepted 0 = tag is not accepted yet, 1 = tag is accepted
	 */
	public function accept_decline_tag($id, $accepted) {
		$conn = getDBConnection();
		$conn->query("UPDATE tags SET accepted=" . $_GET["accepted"] . " WHERE id=" . $_GET["id"] . ";");
		$conn->close();

		echo "{\"status\":\"OK\"}";
	}

	/**
	 * Prints the tags for the specified video. 
	 *
	 * @param $id_type see {@link loadForIdTypeAndIdValue}
	 * @param $id_value see {@link loadForIdTypeAndIdValue}
	 * @param $with_id
	 * <ol><li>0: results are returned as two String arrays (one for all tags and one for only the accepted tags); default value</li>
	 *<li>1: results are returned in one array as JSON-Objects {id: , value: , accepted: } </li></ol>
	 */
	public function get_tags($id_type, $id_value, $with_id = false) {
		$conn = getDBConnection();

		$results = $this->loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}

		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			if($video["status"] == STATUS_HISTORY) {
				echo $this->getTagsForVideoId($video["id"], $with_id, $conn);
			} else {
				echo '{"status":"error","message":"video processing is not finished"}';			
			}

		} else {
			echo '{"status":"error","message":"found no video for given id_value"}';			
		}

		$conn->close();
	}

	/**
	 * Adds a new video to the database. 
	 * 
	 * @param $video_file_url the URL of the video file (probably http://something.something/something.mp4)
	 * @param $video_id assigned id of the video (for later retrieval) (optional)
	 * @param $title Title of the video (optional)
	 * @param $url url of a web page where users can see the video (such as the lokalreporter site) (optional)
	 * @param $preview_image preview image of the video in the Admin UI (optional)
	 */
	public function add_to_queue($video_file_url, $video_id = null, $title = null, $url = null, $preview_image = null) {
		$conn = getDBConnection();

		// build strings $sqlColumns and $sqlValues (could do decomposition...)

		$sqlColumns = "video_url";
		$sqlValues = "\"$video_file_url\"";

		if($video_id != null) {
			$sqlColumns .= ",assigned_id";	
			$sqlValues .= ",\"$video_id\"";			
		}
		if($title != null) {
			$sqlColumns .= ",title";	
			$sqlValues .= ",\"$title\"";			
		}
		if($url != null) {
			$sqlColumns .= ",url";	
			$sqlValues .= ",\"$url\"";			
		}
		if($preview_image != null) {
			$sqlColumns .= ",preview_image";	
			$sqlValues .= ",\"$preview_image\"";			
		}

		$sqlColumns .= ",status";
		$sqlValues .= ",\"" . STATUS_IN_QUEUE . "\"";

		// insert the item into the db

		try {
			$conn->query("INSERT INTO media ($sqlColumns) VALUES ($sqlValues)");
			$generatedId = $conn->insert_id;

			if($generatedId != 0) {

				$queueItem = $conn->query("SELECT * FROM queue WHERE 1 ORDER BY position DESC");

				if($queueItem->num_rows > 0) {
					$result = $queueItem->fetch_assoc();
					$position = intval($result["position"]) + 1;
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES ($generatedId,$position,\"" . STATUS_IN_QUEUE . "\")");
				} else {
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES ($generatedId,1,\"" . STATUS_IN_QUEUE . "\")");
				}

				echo '{"status":"ok"}';
			}

		} catch(Exception $e) {
			exit($e);
		}
		$conn->close();
	}

	/**
	 * Updates the video data. 
	 * 
	 * @param $id_type see {@link loadForIdTypeAndIdValue}
	 * @param $id_value see {@link loadForIdTypeAndIdValue}
	 * 
	 * @param $video_file_url the URL of the video file (probably http://something.something/something.mp4)
	 * @param $video_id assigned id of the video (for later retrieval) (optional)
	 * @param $title Title of the video (optional)
	 * @param $url url of a web page where users can see the video (such as the lokalreporter site) (optional)
	 * @param $preview_image preview image of the video in the Admin UI (optional)
	 */
	public function update_item($id_type, $id_value, $video_file_url, $video_id = null, $title = null, $url = null, $preview_image = null) {
		$conn = getDBConnection();

		$results = $this->loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}		
		if($results->num_rows <= 0) {
			$conn->close();
			exit('{"status":"error","message":"no video found for given id_value"}');
		}

		$video = $results->fetch_assoc();

		$sqlUpdate = $this->buildUpdateItemString($video_file_url, $video_id ,$title, $url, $preview_image, $video["id"]);

		try {
			if($video["status"] == "in_queue") {
				$videoInQueueResults = $conn->query("SELECT * FROM queue WHERE media_id=\"" . $video["id"] . "\"");
				if($videoInQueueResults->num_rows > 0) {
					$videoInQueue = $videoInQueueResults->fetch_assoc();
					$this->updateVideoInQueue($videoInQueue, $video_file_url, $sqlUpdate, $conn);
				} else {
					$conn->close();
					exit('{"status":"error","message":"something went wrong, video should be in queue but could not be found there."}');
				}
			} else {
				$conn->query($sqlUpdate);
				echo '{"status":"ok"}';
			}


		} catch(Exception $e) {
			$conn->close();
			exit($e);
		}
		$conn->close();
	}

	/**
	 * Deletes the video from the database.
	 * May not be possible if the video is currently in the queue and being processed or downloaded at that moment. 
	 *
	 * @param $id_type see {@link loadForIdTypeAndIdValue}
	 * @param $id_value see {@link loadForIdTypeAndIdValue}
	 */
	public function delete_video($id_type, $id_value) {
		$conn = getDBConnection();

		$results = $this->loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}


		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			if($video["status"] == STATUS_IN_QUEUE) {
				$videoInQueue = $conn->query("SELECT * FROM queue WHERE media_id=\"" . $video["id"] . "\"");
				$this->deleteVideoInQueue($videoInQueue, $video["id"], $conn);
			} else if ($video["status"] == STATUS_HISTORY ) {
				$conn->query("DELETE FROM tags WHERE media_id=" . $video["id"]);
				$conn->query("DELETE FROM media WHERE id=\"" . $video["id"] . "\"");
			} else {
				$conn->query("DELETE FROM media WHERE id=\"" . $video["id"] . "\"");
			}

			echo '{"status":"ok"}';

		} else {
			echo '{"status":"error","message":"found no video for given id_value"}';			
		}


		$conn->close();
	}

	/**
	 * Define whether:<ol>
	 * <li>The runscript / main routine will do stuff ("running")</li>
	 * <li>or the runscript will just be idle for a while and no longer do updates ("stop"). Does NOT stop execution of currently running processes!</li>
	 * </ol>
	 * @param queue_status "running" or "stop"
	 */
	public function set_queue_status($queue_status) {
		if($run === "true" || $run === "1" || $run == "running") {
			set(CONFIG_QUEUE_STATUS, "running");
		} else if($run === "false" || $run === "0" || $run == "stop") {
			set(CONFIG_QUEUE_STATUS, "stop");
		} else {
			exit('{"status":"error", "message" : "invalid value"}');
		}

		echo '{"status":"ok"}';		
	}

	/**
	 * Returns the status of the video (what is currently happening to it)
	 * <br>
	 * Uses the status in the media table if the item is not in the queue
	 * and the status in the queue table if it is.
	 * 
	 * @param $id_type see {@link loadForIdTypeAndIdValue}
	 * @param $id_value see {@link loadForIdTypeAndIdValue}
	 */
	public function get_status($id_type, $id_value) {
		$conn = getDBConnection();

		$results = $this->loadForIdTypeAndIdValue($id_type, $id_value, $conn);
		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}

		if($results->num_rows > 0) {
			$video = $results->fetch_assoc();

			if($video["status"] != STATUS_IN_QUEUE) {
				echo '{"status" : "ok", "video_status" : "' . $video['status'] . '"}';
			} else {
				$videoInQueueResults = $conn->query("SELECT * FROM queue WHERE media_id=" . $video["id"]);

				if($videoInQueueResults->num_rows > 0) {
					$videoInQueue = $videoInQueueResults->fetch_assoc();
					echo '{"status" : "ok", "video_status" : "' . $videoInQueue['status'] . '"}';
				} else {
					exit('{"status":"error", "message":"video should be in queue but was not found there."}');
				}
			}

		} else {
			$conn->close();
			exit('{"status":"error","message":"found no video for given id_value"}');			
		}

		$conn->close();
	}

	/**
	 * If there was an error while the video was being processed and you're confident it has been fixed,
	 * you can try processing it again and append it to the queue again using this api method.
	 * Alternatively, you could delete it and add it again.
	 *
	 * @param $id_type see {@link loadForIdTypeAndIdValue}
	 * @param $id_value see {@link loadForIdTypeAndIdValue}
	 */
	public function try_video_again($id_type, $id_value) {
		$conn = getDBConnection();

		$results = $this->loadForIdTypeAndIdValue($id_type, $id_value, $conn);

		if($results == null) {
			$conn->close();
			exit('{"status":"error","message":"invalid id_type"}');
		}		
		if($results->num_rows <= 0) {
			$conn->close();
			exit('{"status":"error","message":"no video found for given id_value"}');
		}

		$video = $results->fetch_assoc();

		try {
			if($video["status"] == STATUS_EVALUATING_ERROR || $video["status"] == STATUS_SEGMENTING_ERROR ||
				$video["status"] == STATUS_PROCESSING_ERROR || $video["status"] == STATUS_DOWNLOAD_ERROR) {
				
				$queueItem = $conn->query("SELECT * FROM queue WHERE 1 ORDER BY position DESC");

				if($queueItem->num_rows > 0) {
					$queueResult = $queueItem->fetch_assoc();
					$position = intval($queueResult["position"]) + 1;
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES (" . $video["id"] . ",$position,\"" . STATUS_IN_QUEUE . "\")");
				} else {
					$conn->query("INSERT INTO queue (media_id,position,status) VALUES (" . $video["id"] . ",1,\"" . STATUS_IN_QUEUE . "\")");
				}

				$conn->query("UPDATE media SET status=\"" . STATUS_IN_QUEUE ."\" WHERE id=" . $video["id"]);
				echo '{"status" : "ok"}';
				
			} else {
				$conn->close();
				exit('{"status":"error","message":"no video found for given id_value"}');
			}
		} catch(Exception $e) {
			$conn->close();
			exit($e);
		}
		$conn->close();
	}

	/**
	 * decomposition method for {@link #update_item}, because if the video is in the queue, there needs to be some
	 * consideration as to whether or not the update can currently be executed.<br>
	 * if the video has been downloaded, that will be reset if the video url changed.<br>
	 * The update can only be executed if the video is not currently being processed!
	 */
	private function updateVideoInQueue($videoInQueue, $video_file_url, $sqlUpdate, $conn) {
		$queueStatus = $videoInQueue["status"];

		if($queueStatus == STATUS_DOWNLOADING) {
			$conn->close();
			exit('{"status":"error","message":"cannnot change video while it is being downloaded"}');
		} else if ($queueStatus == STATUS_DOWNLOADED) {
			if($video["video_url"] != $video_file_url) {
				$conn->query("UPDATE queue SET status=\"" . STATUS_IN_QUEUE . "\" WHERE id=" . $videoInQueue["id"]);
				// no need to clean up (I think), video will be overwritten if the new url is working.
				// probably doesn't change, anyways...
			}
			$conn->query($sqlUpdate);
			echo '{"status":"ok"}';
		} else if ($queueStatus == STATUS_IN_QUEUE) {
			$conn->query($sqlUpdate);
			echo '{"status":"ok"}';
		} else { // could make more cases if the timing is just right but... no.
			$conn->close();
			exit('{"status":"error","message":"cannnot change video while it is being processed"}');
		}
	}

	/**
	 * decomposition method for {@link #update_item}, builds an SQL string to update the item with the given parameters.
	 * @param $videoDbId db_id (in the API lingo) / id column value of the video in the media table
	 */
	private function buildUpdateItemString($video_file_url, $assigned_id = null, $title = null, $url = null, $preview_image = null, $videoDbId) {
		$sqlUpdate = "UPDATE media SET `video_url`=\"$video_file_url\",";

		if($assigned_id != null) {
			$sqlUpdate .= "`assigned_id`=\"$assigned_id\",";			
		} else {
			$sqlUpdate .= "`assigned_id`=null,";
		}

		if($title != null) {
			$sqlUpdate .= "`title`=\"$title\",";			
		} else {
			$sqlUpdate .= "`title`=null,";
		}

		if($url != null) {
			$sqlUpdate .= "`url`=\"$url\",";			
		} else {
			$sqlUpdate .= "`url`=null,";
		}

		if($preview_image != null) {
			$sqlUpdate .= "`preview_image`=\"$preview_image\"";			
		} else {
			$sqlUpdate .= "`preview_image`=null";
		}

		$sqlUpdate .= " WHERE id=" . $videoDbId;
		return $sqlUpdate;
	}	

	/**
	 * decomposition method for {@link #get_tags}. Returns the response Strings for the API call
	 * to get_tags; retrieves the tags stored for a video.
	 * 
	 * @param $videoId db_id / id column value of the video
	 * @param $with_id see get_tags
	 * @param $conn open SQL connection
	 */
	private function getTagsForVideoId($videoId, $with_id, $conn) {
		$tagsResults = $conn->query("SELECT * FROM tags WHERE media_id=\"" . $videoId . "\"");

		$acceptedTagsArray = array();
		$tagsArray = array();

		if($tagsResults->num_rows > 0) {
			while($tagResult = $tagsResults->fetch_assoc()) {
				if($with_id) {
					$tagsArray[] = array("id" => $tagResult["id"], "value" => $tagResult["content"], "accepted" => $tagResult["accepted"]);
				} else {
					$tagsArray[] = $tagResult["content"];
					if($tagResult["accepted"]) {
						$acceptedTagsArray[] = $tagResult["content"];
					}
				}
			}
		}

		if($with_id) {
			return json_encode(array('status' => "ok", 'tags' => $tagsArray));
		} else {
			return json_encode(array('status' => "ok", 'tags' => $tagsArray, 'accepted_tags' => $acceptedTagsArray));
		}
	}

	/**
	 * decomposition method for {@link #delete_video};
	 * if the video has been downloaded already, this deletes the video file.
	 */
	private function deleteVideoFile($id) {
		$currentPath = realpath(dirname(__FILE__));

		$videoPath = substr($currentPath, 0, strrpos($currentPath, "/"));
		$videoPath = substr($videoPath, 0, strrpos($videoPath, "/"));

		$videoPath .= '/video_downloads/' . $id . ".mp4";

		try {
			unlink($videoPath);
		} catch (Exception $ignored) {

		}
	}

	/**
	 * decomposition method for {@link #delete_video}; <br>
	 * in the Queue, it might be impossible to delete a video (if it is currently being processed or downloaded).
	 * Therefore, this may or may not actually delete the video (will exit with error if it can't)
	 */
	private function deleteVideoInQueue($videoInQueue, $videoId, $conn) {
		if($videoInQueue->num_rows > 0) {
			$assocRow = $videoInQueue->fetch_assoc();
			if($assocRow["status"] == STATUS_DOWNLOADED) {
				$this->deleteVideoFile($video["id"]);
			}

			if ($assocRow["status"] == STATUS_DOWNLOADING) {
				$conn->close();
				exit('{"status":"error","message":"cannot delete video while it is being downloaded"}'); // mostly for our convenience...
			} else if(!in_array($assocRow["status"], array(
				STATUS_DOWNLOADED, STATUS_IN_QUEUE, STATUS_EVALUATING_ERROR, STATUS_SEGMENTING_ERROR,
				STATUS_PROCESSING_ERROR, STATUS_DOWNLOAD_ERROR))) {
				$conn->close();
				exit('{"status":"error","message":"cannot delete video while it is being processed"}'); // mostly for our convenience...
			}
		}

		$conn->query("DELETE FROM queue WHERE media_id=\"" . $videoId . "\"");
		$conn->query("DELETE FROM media WHERE id=\"" . $videoId . "\"");
		// could fix positions (but that happens in runscript anyway...)
	}

	/**
	 * decomposition method that allows multiple types of queries
	 * (we cannot predict how exactly the software will be used at this point, so being flexible won't hurt).
	 * 
	 * @param $id_type possible values: video_id, db_id or video_file_url; will return null otherwise
	 * @param $id_value value for the specified video and id_type
	 * @param $conn open SQL connection
	 */
	public function loadForIdTypeAndIdValue($id_type, $id_value, $conn) {
		$results = null;

		// LIBRARY ODER UTIL ODER SO FÜR DAS HIER!!
		if($id_type == "video_id") {
			$results = $conn->query("SELECT * FROM media WHERE assigned_id=\"$id_value\"");
		} else if ($id_type == "db_id") {
			$results = $conn->query("SELECT * FROM media WHERE id=\"$id_value\"");
		} else if ($id_type == "video_file_url") {
			$results = $conn->query("SELECT * FROM media WHERE video_url=\"$id_value\"");
		}

		return $results;
	}

}