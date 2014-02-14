<?php
	$MYSQL_CONFIG_LOC = "config/MySQLConfig.php";

    function get_mysql_connection() {
		static $mysqli;
		if (!isset($mysqli)) {
			if (!class_exists("mysqli")) {
				error_log("Unable to connect to MySQL database: MySQLi PHP extension not installed");
				die("ERROR: Check to make sure the MySQLi extension is installed on server");
			}
			if (!file_exists($MYSQL_CONFIG_LOC)) {
				error_log("MySQL config file did not exist, using defaults");
				copy("config/DefaultMySQLConfig.txt", $MYSQL_CONFIG_LOC);
			}
			require_once $MYSQL_CONFIG_LOC;
			$mysqli = new mysqli($MYSQL_HOST, $MYSQL_USERNAME, $MYSQL_PASSWORD, $PLAY_BY_POST_DB, $MYSQL_PORT, $MYSQL_SOCKET);
			if ($mysqli->connect_error) {
				error_log("Error connecting to MySQL server (Error #$mysqli->connect_errno): $mysqli->connect_error");
				die("Error connecting to MySQL server (Error #$mysqli->connect_errno): $mysqli->connect_error");
			}
		}
		return $mysqli;
	}
?>