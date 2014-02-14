<?php

define("FAST_ALGO", "crc32");

function login($username, $password) {
	static $stmt;
	if (!isset($stmt)) {
		require_once 'MySQLConnector.php';
		$stmt = get_mysql_connection()->prepare("SELECT id, username, passwdhash FROM users WHERE namehash = UNHEX(?) LIMIT 1");	
	}
	$stmt->bind_param("s", $userhash);
	$userhash = hash(FAST_ALGO, $username);
	$stmt->execute();
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
		$stmt = get_mysql_connection()->prepare("INSERT INTO users (username, namehash, passwdhash) VALUES (?, UNHEX(?), ?)");
	}
	$stmt->bind_param("sss", $username, $userhash, $passwdhash);
	$userhash = hash(FAST_ALGO, $username);
	$passwdhash = password_hash($password, PASSWORD_DEFAULT);
	$stmt->execute();
	return $stmt->errno == 0;	// An error means we tried
								// to insert a namehash that already existed
}								// (or have overflowed id) so return false

function logout() {
	unset($_SESSION["uid"]);
	unset($_SESSION["username"]);
}

?>