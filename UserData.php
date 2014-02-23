<?php

define("FAST_ALGO", "crc32"); // define the fast hashing algorithm to use on the username

/**
 * function to login a user if they are stored in the database
 */
function login($username, $password) {
	static $stmt; // I don't know if declaring the statement once and saving it as a static variable is correct
	if (!isset($stmt)) { // if the statement hasn't been created yet
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		if (!$stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username_hash = UNHEX(?) LIMIT 1")) {
			// if there is an error
			unset($stmt); // unset $stmt so that the !isset($stmt) functions as it should
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
	}
	$stmt->bind_param("s", $userhash);
	$userhash = hash(FAST_ALGO, $username); // using a hash instead of the plaintext username makes the search faster
	if (!$stmt->execute()) { // if there was an error executing the statement
		error_log("(Error #$stmt->errno): $stmt->error");
		die("(Error #$stmt->errno): $stmt->error");
	}
	$stmt->bind_result($id, $user, $passwdhash);
	if ($stmt->fetch() && password_verify($password, $passwdhash)) {
		// if a username hash matched the search and the password is correct
		$_SESSION["uid"] = $id; // uid is for checking if a user is logged in
		$_SESSION["username"] = $user; // username is also kept because it will get used a lot
		$stmt->free_result();
		return TRUE;
	} else {
		$_SESSION["uid"] = 0; // the id of a user can't be 0 so I set the UID to 0 so that
							  // I know there was a failed login attempt
		$stmt->free_result();
		return FALSE;
	}
}

/**
 * Function that adds a new user to the database
 */
function create_pbp_user($username, $password) {
	if (strlen($username) > 20 || strlen($username) < 1) {
		// the database column for the username can only store a maximum of 20 characters
		return FALSE;
	}
	static $stmt;
	if (!isset($stmt)) {
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		if (!$stmt = $mysqli->prepare("INSERT INTO users (username, username_hash, password_hash) VALUES (?, UNHEX(?), ?)")) {
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
	}
	$stmt->bind_param("sss", $username, $userhash, $passwdhash);
	$userhash = hash(FAST_ALGO, $username);
	$passwdhash = password_hash($password, PASSWORD_DEFAULT); // this is more secure for hashing the hash()
	if (!$stmt->execute() && $stmt->errno != 1062) { //1062 is the error for duplicate value
		error_log("(Error #$stmt->errno): $stmt->error");
		die("(Error #$stmt->errno): $stmt->error");
	}
	return $stmt->errno == 0;	// An error means we tried
								// to insert a username hash that already existed
}								// (or have overflowed id) so return false

/**
 * This function allows me to change the login process from one place
 */
function logout() {
	unset($_SESSION["uid"]);
	unset($_SESSION["username"]);
}

?>