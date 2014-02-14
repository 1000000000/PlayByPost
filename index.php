<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	session_start();
	if (!isset($_GET["action"])) $_GET["action"] = "";
	if ($_GET["action"] == "logout") {
		require_once 'UserData.php';
		logout();
	}
	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] < 1) {
		header("Location: login.php");
		exit;
	}
	switch ($_GET["action"]) {
		case "mychars":
			$GLOBALS["page_title"] = "Characters";
			$GLOBALS["create_page"] = "create_chars_body";
			break;
		case "profile":
			$GLOBALS["page_title"] = "Profile";
			$GLOBALS["create_page"] = "create_profile_body";
			break;
		default: //includes "" so index.php will go here
			$GLOBALS["page_title"] = "Games";
			$GLOBALS["create_page"] = "create_games_body";
	}
	
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="stylesheet.css" />
		<title>Simple Play By Post</title>
	</head>
	<body>
	<div id="titlebar">
		<ul>
			<li><span><em>Welcome <?php echo $_SESSION["username"] ?></em></span></li>
			<li><a href="index.php"><span>Home</span></a></li>
			<li><a href="index.php?action=mychars"><span>My Characters</span></a></li>
			<li><a href="index.php?action=profile"><span>Profile</span></a></li>
			<li><a href="index.php?action=logout"><span>Logout</span></a></li>
		</ul>
		<br />
	</div>
	<div>
		<h2 class="title"><?php echo $GLOBALS["page_title"] ?></h2><br />
		<div id="body">
			<?php $GLOBALS["create_page"]() ?>
		</div>
	</div>
	</body>
</html>

<?php
	
	function println($string, $indent=0) {
		echo str_pad($string, $indent + strlen($string), "\t", STR_PAD_LEFT);
		echo "\n";
	}

	function create_games_body() {
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		$res = $mysqli->query("
			SELECT game_name, char_name, name, activity FROM (
				SELECT games.name AS game_name, gm AS gmid, characters.name AS char_name, activity
				FROM characters
				JOIN games ON (
					characters.userid = " . $_SESSION["uid"] . "
					AND gameid = games.id
				) ORDER BY activity DESC
			) AS t JOIN characters ON (id = gmid)
		");
		if ($res->num_rows < 1) {
			println("<h3>You are not part of any games!</h3>");
		} else {
			println("<table>");
			println("<tr>", 4);
			println("<th>Name</th>", 5);
			println("<th>Character</th>", 5);
			println("<th>Last Activity</th>", 5);
			
			println("</tr>", 4);
			println("</table>", 3);
		}
	}
	
	function create_chars_body() {
		
	}
	
	function create_profile_body() {
		
	}

?>
