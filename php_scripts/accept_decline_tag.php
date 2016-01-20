<?php
	require_once("util/db_connection.php");

	/*
	 * Call this script via ajax or some HTTP-Get-Request with the parameters:
	 * accept_decline_tag.php?accepted=[0 OR 1]&id=[TAG_ID, INTEGER]
	 */
	
	$conn = getDBConnection();
	$conn->query("UPDATE tags SET accepted=" . $_GET["accepted"] . " WHERE id=" . $_GET["id"] . ";");
	$conn->close();

	echo "{\"status\":\"OK\"}";

?>