<?php
	require_once("dbConnection.php");
	
	$conn = getDBConnection();
	$conn->query("UPDATE tags SET accepted=" . $_GET["accepted"] . " WHERE id=" . $_GET["id"] . ";");
	$conn->close();

	echo "{\"status\":\"OK\"}";

?>