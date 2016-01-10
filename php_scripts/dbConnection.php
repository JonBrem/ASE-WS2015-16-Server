<?php

	function getDBConnection() {
		$server = "localhost";
		$username = "root";
		$pw = "";
		$dbname = "ase_text_in_images";

		$conn = new mysqli($server, $username, $pw, $dbname);

		return $conn;
	}

?>