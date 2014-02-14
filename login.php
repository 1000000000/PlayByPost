<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	require_once 'UserData.php';
	session_start();
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		login($_POST["username"], $_POST["password"]);
	}
	if (isset($_SESSION["username"])) {
		header("Location: index.php");
		exit;
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<title>Log In</title>
	</head>
	<body>
	<div>
		<h2 class="title">Simple Play by Post</h2><br />
		<form id="body" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
			<table>
				<tr>
					<td><?php if (isset($_SESSION["uid"])) echo "<h5 class=\"err\">Incorrect Username or Password</h5>"; ?></td>
				</tr>
				<tr>
					<td>Username: </td>
					<td><input type="text" name="username" value="<?php if (isset($_SESSION["uid"])) { echo $_POST["username"]; unset($_SESSION["uid"]); } ?>" /></td>
				</tr>
				<tr>
					<td>Password: </td>
					<td><input type="password" name="password" /></td>
				</tr>
				<tr>
					<td><input type="submit" value="Log In" /></td>
				</tr>
				<tr>
					<td><a class="small" href="signup.php">Create an Account</a></td>
				</tr>
			</table>
		</form>
	</div>
	</body>
</html>
