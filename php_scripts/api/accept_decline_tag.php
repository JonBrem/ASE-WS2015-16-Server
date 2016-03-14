<?php
	require_once("../util/db_connection.php");

	/*
	 * Call this script via ajax or some HTTP-Get-Request with the parameters:
	 * accept_decline_tag.php?accepted=[0 OR 1]&id=[TAG_ID, INTEGER]
	 */
	function accept_decline_tag($id, $accepted) {
		$conn = getDBConnection();
		$conn->query("UPDATE tags SET accepted=" . $_GET["accepted"] . " WHERE id=" . $_GET["id"] . ";");
		$conn->close();

		echo "{\"status\":\"OK\"}";
	}

	if(array_key_exists("id", $_GET) && array_key_exists("accepted", $_GET)) {
		accept_decline_tag($_GET["id"], $_GET["accepted"]);
	} else {
		exit('{"status":"error", "message":"parameters id and accepted need to be set."}');
	}

?>