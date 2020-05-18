<?php
// DESC: prompts for username and password
	include_once "user.inc";
	global $user;

	session_start();
	include_once "session.inc";
	$_SESSION['user'] = $user;
	//session_register("user");

	include_once "cards.inc";
	$project = "3x5 Cards";

	if (isset($_POST["clear"])) {
		$_POST["username"] = "";
		$_POST["passwd"] = "";
		$username = "";
		$passwd = "";
		$login_user = 0;
	}
	if (isset($_POST["username"])) {
		$username = strip_tags(trim($_POST["username"]));
	} else {
		$username = "";
	}
	if (isset($_POST["passwd"])) {
		$passwd = strip_tags(trim($_POST["passwd"]));
	} else {
		$passwd = "";
	}
	if (isset($_GET["access"])) {
		$username = $user->uname;
		$passwd = "";
		session_destroy();
		session_write_close();
		$_GET["access"] = preg_replace("/\.access/","",$_GET["access"]);
	}

	function not_logged_in ($q) {
		global $username;
		global $passwd;
		global $project;
		global $version;
		$hlogin = sendhelp("$project - Login","login");
		$husername = sendhelp("Username","login username");
		$hpassword = sendhelp("Password","login password");
		$q = warn($q);

		card_head("User Login");
print <<<NOT_LOGGED_IN
$hlogin
<br/>
Version: $version
<p>
NOT_LOGGED_IN;
print table(row(head(
	form($_SERVER['PHP_SELF'],
	table(	row(cell($husername,"class=\"h_form\"")
		.cell(input("text","username",$username,"size=25")))
		.row(cell($hpassword,"class=\"h_form\"")
		.cell(input("password","passwd",$passwd,"size=25")))
		.row(head(
			input("submit","submit","Login")
			.input("reset","reset","Reset")
			.input("submit","clear","Clear")
			.input("hidden","login_user","1")
		,"colspan=2"))
	,"class=\"form\"")
	.row(head(form("create_user.php",
		input("submit","create_update_user","Create User")
	)))
))),"class=\"tight\"");
print <<<NOT_LOGGED_IN
$q
</center>
<p>
NOT_LOGGED_IN;

showphpinfo();

	card_foot();
}

// if coming to reset login
	if (! isset($_POST["username"])) {
		not_logged_in(
		"Please fill in fields to login into ``$project''");
		return;
	}

// if valid user then go to frames
	include_once "3x5_db.inc";
	$db = new i3x5_DB($schema);

// see if the username matches then proceed
	$uid = $db->sql(
"SELECT uid FROM i3x5_userpass WHERE username='$username'");
	if (! $uid) {
		not_logged_in("Invalid Username");
		return;
	}

// find the matching password and access_level

	$access_level = 0;
	$pswd = array(
		"passwd_admin" => $level_admin,
		"passwd_w" => $level_write,
		"passwd_a" => $level_append,
		"passwd_r" => $level_read);
	reset($pswd);
	while (list($k,$v) = each($pswd)) {
		if (1 == $db->sql(
			"SELECT count(*) FROM i3x5_userpass ".
			"WHERE username='$username' AND $k='$passwd'")) {
				$access_level=$v;
		}
	}

	if ($access_level) {

		$project = $db->sql(
			"SELECT project FROM i3x5_userpass ".
			"WHERE username='$username'");
		$_SESSION['user']
			= New User($uid, $username,$access_level,$project,

			$db->bids($uid));

		header("Location: index.php");

	} else {
		not_logged_in("Password does not match at any level!");
	}
?>
