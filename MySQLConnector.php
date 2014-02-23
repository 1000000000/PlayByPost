<?php
	
	/**
	 * This method is a convenience method to get an instance
	 * of the mysqli connection to the mysql server
	 */
    function get_mysql_connection() {
		static $mysqli; // I'm not sure whether it helps if I connect only once
						// and store the connection in a static variable
		if (!isset($mysqli)) { // if I haven't connected
			if (!class_exists("mysqli")) { // if the PHP mysqli extension does not exist
				error_log("Unable to connect to MySQL database: MySQLi PHP extension not installed");
				die("ERROR: Check to make sure the MySQLi extension is installed on server");
			}
			if (!file_exists(get_mysql_config_loc())) { // if the config file doesn't exist
				// write to log and create it from the default
				error_log("MySQL config file did not exist at " . get_mysql_config_loc() . ", using defaults");
				copy("config/DefaultMySQLConfig.txt", $MYSQL_CONFIG_LOC);
			}
			require_once get_mysql_config_loc(); // require the config
			$mysqli = new mysqli($MYSQL_HOST, $MYSQL_USERNAME, $MYSQL_PASSWORD, $PLAY_BY_POST_DB, $MYSQL_PORT, $MYSQL_SOCKET);
			if ($mysqli->connect_error) {
				unset($mysqli); // unset the static $mysqli variable
								// so that we try to connect again next time it is called
				error_log("Error connecting to MySQL server (Error #$mysqli->connect_errno): $mysqli->connect_error");
				die("Error connecting to MySQL server (Error #$mysqli->connect_errno): $mysqli->connect_error");
			}
		}
		return $mysqli;
	}
	
	/**
	 * For some reason globals and define() weren't working for me so this
	 * is my workaround to store the location of the mysql config file
	 */
	function get_mysql_config_loc() {
		return "config/MySQLConfig.php";
	}
?>