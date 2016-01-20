<?php

	/**
	 * Helper method that can be included in any other files and returns
	 * a database connection with pre-set parameters..
	 * Don't forget to close the connection after using it!
	 */
	function getDBConnection($server = "localhost", $username = "root", 
			$pw = "", $dbname = "ase_text_in_images") {

		$conn = new mysqli($server, $username, $pw, $dbname);

		return $conn;
	}

?>