<?php

define("FAST_ALGO", "crc32");

function login($username, $password) {
	static $stmt;
	if (!isset($stmt)) {
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		if (!$stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username_hash = UNHEX(?) LIMIT 1")) {
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
	}
	$stmt->bind_param("s", $userhash);
	$userhash = hash(FAST_ALGO, $username);
	if (!$stmt->execute()) {
		error_log("(Error #$stmt->errno): $stmt->error");
		die("(Error #$stmt->errno): $stmt->error");
	}
	$stmt->bind_result($id, $user, $passwdhash);
	if ($stmt->fetch() && password_verify($password, $passwdhash)) {
		$_SESSION["uid"] = $id;
		$_SESSION["username"] = $user;
		$stmt->free_result();
		return TRUE;
	} else {
		$_SESSION["uid"] = 0;
		$stmt->free_result();
		return FALSE;
	}
}

function create_pbp_user($username, $password) {
	if (strlen($username) > 20 || strlen($username) < 1) {
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
	$passwdhash = password_hash($password, PASSWORD_DEFAULT);
	if (!$stmt->execute() && $stmt->errno != 1062) { //1062 is the error for duplicate value
		error_log("(Error #$stmt->errno): $stmt->error");
		die("(Error #$stmt->errno): $stmt->error");
	}
	return $stmt->errno == 0;	// An error means we tried
								// to insert a username hash that already existed
}								// (or have overflowed id) so return false

function logout() {
	unset($_SESSION["uid"]);
	unset($_SESSION["username"]);
}

?>