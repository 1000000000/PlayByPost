<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	require_once 'UserData.php';
	session_start();
	if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password2"])) {
		if (!(strlen($_POST["username"]) > 0 && strlen($_POST["password"]) > 0 && strlen($_POST["password2"])) > 0) {
			$GLOBALS["err"] = "All the fields must be filled";
		} elseif (!ctype_alnum(str_replace(array("-", "_"), "", $_POST["username"]))) {
			$GLOBALS["err"] = "Username may only contain alphanumeric characters, \"-\" and \"_\"";
		} elseif (strlen($_POST["username"]) > 20) {
			$GLOBALS["err"] = "Username exceeds 20 character limit";
		} elseif (!create_pbp_user($_POST["username"], $_POST["password"])) {
			$GLOBALS["err"] = "Username already exists";
		} elseif ($_POST["password"] != $_POST["password2"]) {
			$GLOBALS["err"] = "Passwords do not match";
		} else {
			login($_POST["username"], $_POST["password"]);
			header("Location: index.php");
			exit;
		}
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<title>Sign Up</title>
	</head>
	<body>
	<div>
		<h2 class="title">Sign Up</h2><br />
		<form id="body" method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
			<?php if (isset($GLOBALS["err"])) echo "<h5 class=\"err\">" . $GLOBALS["err"] . "</h5>"; ?>
			<table>
				<tr>
					<td>Username: </td>
					<td><input type="text" name="username" value="<?php if (isset($GLOBALS["err"]) && isset($_POST["username"])) echo htmlspecialchars($_POST["username"]); ?>" /></td>
				</tr>
				<tr>
					<td>Password: </td>
					<td><input type="password" name="password" /></td>
				</tr>
				<tr>
					<td>Retype Password: </td>
					<td><input type="password" name="password2" /></td>
				</tr>
				<tr>
					<td><input type="submit" value="Sign up" /></td>
				</tr>
				<tr>
					<td><a class="small" href="index.php">Home</a></td>
				</tr>
			</table>
		</form>
	</div>
	</body>
</html>
