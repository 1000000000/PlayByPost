<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	session_start();
	if (!isset($_GET["action"])) $_GET["action"] = ""; // This prevents warnings when I check what the action is
	if ($_GET["action"] == "logout") {
		require_once 'UserData.php'; // logout() is in UserData.php
		logout();
	}
	if (!isset($_SESSION["uid"]) || $_SESSION["uid"] < 1) { // if no one is logged in
		header("Location: login.php"); // redirect to login
		exit;
	}
	switch ($_GET["action"]) {
		case "mychars": // page for a users characters
			$GLOBALS["page_title"] = "Characters";
			$GLOBALS["create_page"] = "create_chars_body"; // sets function to create page body
			break;
		case "profile": // page for user profile (not done)
			$GLOBALS["page_title"] = "Profile";
			$GLOBALS["create_page"] = "create_profile_body";
			break;
		case "game": // this is the case for a user's list of games or a the forum for a game
			if (isset($_GET["gameid"]) && $gameid = intval($_GET["gameid"]) > 0) {
				require_once 'MySQLConnector.php';
				$mysqli = get_mysql_connection();
				$res = $mysqli->query("
					SELECT COUNT(*) AS num_chars, games.name AS game_name
					FROM characters
					JOIN (
						character_game_map
						JOIN games ON ( games.id = $gameid
						AND games.id = character_game_map.game_id)
					) ON ( character_game_map.character_id = characters.id)
					WHERE characters.user_id = ${_SESSION["uid"]}"
				);
				if (!$res) { // if there was an error
					error_log("(Error #$mysqli->errno): $mysqli->error");
					die("(Error #$mysqli->errno): $mysqli->error");
				}
				$row = $res->fetch_assoc();
				if ($row["num_chars"] > 0) {
					$GLOBALS["page_title"] = $row["game_name"];
					$GLOBALS["create_page"] = "create_forum_body";
					$GLOBALS["game_id"] = $gameid;
					break;
				}
			} // we want to go to the games page if the gameid GET variable does not exist or is invalid
		default: //includes "" so index.php will go here
			// the default is to show the list of games the user is in
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
	<!-- This is a menu which runs across the top of the page -->
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
			<!-- PHP starts here -->
			<?php $GLOBALS["create_page"]() ?>
			<!-- PHP ends here -->
		</div>
	</div>
	</body>
</html>

<?php
	
	/**
	 * I like nicely formatted HTML
	 * The purpose of this method is to replace my use of echo when echoing
	 * large blocks of HTML.
	 * It indents by the specified amount (or none) and puts a newline
	 * after the printed string
	 */
	function println($string, $indent=0) {
		echo str_pad($string, $indent + strlen($string), "\t", STR_PAD_LEFT);
		echo "\n";
	}

	function create_games_body() {
		require_once "MySQLConnector.php";
		$mysqli = get_mysql_connection();
		$res = $mysqli->query("
			SELECT games.id AS id, games.name AS game_name,
			characters.name AS char_name, gms.name AS gm_name,
			DATE_FORMAT(games.last_activity, \"%M %d, %Y, %r\") AS activity
			FROM characters AS gms
			JOIN (
				games
				JOIN (
					character_game_map
					JOIN (
						characters
						JOIN users ON ( users.id = ${_SESSION["uid"]}
						AND users.id = characters.user_id )
					) ON ( characters.id = character_game_map.character_id )
				) ON ( character_game_map.game_id = games.id )
			) ON ( games.gm_id = gms.id ) ORDER BY games.last_activity DESC
		");
		if(!$res) { // if there was an error
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
		if ($res->num_rows < 1) { // if no rows are returned
			println("<h3>You are not part of any games!</h3>");
		} else { // create table for games
			// opens the table tag and creates table header
			println("<table class=\"bordered\">"); // class bordered is for tables with visible borders
			println("<tr>", 4);
			println("<th>Name</th>", 5);
			println("<th>Character</th>", 5);
			println("<th>GM</th>", 5);
			println("<th>Last Activity</th>", 5);
			println("</tr>", 4);
			// for all of the rows fetched from the database add a row to the HTML table
			while ($row = $res->fetch_assoc()) {
				println("<tr>", 4);
				// The game name will link to the game's forum page
				println("<td><a href=\"index.php?action=game&gameid=${row["id"]}\">${row["game_name"]}</a></td>", 5);
				println("<td>${row["char_name"]}</td>", 5);
				println("<td>${row["gm_name"]}</td>", 5);
				println("<td>${row["activity"]}</td>", 5);
				println("</tr>", 4);
			}
			println("</table>", 3);
		}
	}
	
	function create_chars_body() {
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		$res = $mysqli->query("
			SELECT games.id AS id, characters.name AS name, games.name AS game
			FROM games
			RIGHT JOIN (
				character_game_map
				RIGHT JOIN (
					characters
					JOIN users ON ( users.id = ${_SESSION["uid"]}
					AND users.id = characters.user_id )
				) ON ( characters.id = character_game_map.character_id )
			) ON ( character_game_map.game_id = games.id )
		");
		if(!$res) {
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
		if ($res->num_rows < 1) {
			println("<h3>You do not have any characters!</h3>");
		} else {
			println("<table class=\"bordered\">");
			println("<tr>", 4);
			println("<th>Name</th>", 5);
			println("<th>Game</th>", 5);
			println("</tr>", 4);
			while ($row = $res->fetch_assoc()) {
				println("<tr>", 4);
				println("<td>${row["name"]}</td>", 5);
				// if the game is NULL (the character is not associated with a game)
				// the games column will say "None" otherwise it will have a link to the game
				println("<td>" . ($row["game"] == NULL ? "None" : "<a href=\"index.php?action=game&gameid=${row["id"]}\">${row["game"]}</a>") . "</td>", 5);
				println("</tr>", 4);
			}
			println("</table>", 3);
		}
	}
	
	function create_profile_body() {
		println("Under construction...");
	}
	
	function create_forum_body() {
		require_once 'MySQLConnector.php';
		$mysqli = get_mysql_connection();
		$res = $mysqli->query("
			SELECT characters.name AS char_name, users.username AS username,
			posts.created AS posted, posts.content AS content
			FROM users
			JOIN (
				characters JOIN posts
				ON ( posts.game_id = ${GLOBALS["game_id"]}
				AND characters.id = posts.character_id )
			) ON ( users.id = characters.user_id )
			ORDER BY posts.created
		");
		if (!$res) { // if there was an error
			error_log("(Error #$mysqli->errno): $mysqli->error");
			die("(Error #$mysqli->errno): $mysqli->error");
		}
		if ($res->num_rows < 1) {
			println("<h3>No posts have been made on this game</h3>");
		} else {
			println("<table id=\"posts\">");
			while ($row =$res->fetch_assoc()) {
				println("<tr>", 4);
				println("<td>", 5);
				println("<h4>${row["char_name"]}</h4>", 6);
				println("<p>${row["username"]}</p>", 6);
				println("<p class=\"small\">Posted: ${row["posted"]}</p>");
				println("</td>", 5);
				println("<td>", 5);
				foreach (parse_post_content($row["content"]) as $line) {
					println("$line<br />", 6);
				}
				println("</td>", 5);
				println("</tr>", 4);
			}
			println("</table>", 3);
		}
	}

	function parse_post_content($str) {
		return array("foobar");
	}

?>
