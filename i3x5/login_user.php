<?php
// DESC: prompts for username and password
	include_once "user.inc";

	session_start();
	session_register("user");

	include_once "cards.inc";
	$project = "3x5 Cards";

	if ($HTTP_POST_VARS["clear"]) {
		$HTTP_POST_VARS["username"] = "";
		$HTTP_POST_VARS["passwd"] = "";
		$username = "";
		$passwd = "";
		$login_user = 0;
	}
	if ($HTTP_POST_VARS["username"]) {
		$username = strip_tags(trim($HTTP_POST_VARS["username"]));
	}
	if ($HTTP_POST_VARS["passwd"]) {
		$passwd = strip_tags(trim($HTTP_POST_VARS["passwd"]));
	}

	function not_logged_in ($q) {
		global $PHP_SELF;
		global $username;
		global $passwd;
		global $form_color;
		global $box_color;
		global $project;
		global $phpinfo;
		global $version;
		$hlogin = sendhelp("$project - Login","login");
		$husername = sendhelp("Username","login username");
		$hpassword = sendhelp("Password","login password");
		$q = warn($q);
print <<<NOT_LOGGED_IN
<HTML>
<HEAD>
<TITLE>$project - Login </TITLE>
</HEAD>
<BODY>
<CENTER>
$hlogin
<BR>
Version: $version
<P>
<TABLE BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BCOLOR="$box_color">
<TR><TH>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TD>$husername</TD><TD>
	<INPUT NAME="username" SIZE="25" TYPE="text" value="$username" >
	</TD></TR>
	<TR><TD>$hpassword</TD><TD>
	<INPUT NAME="passwd" SIZE="25" TYPE="password" value="$passwd" >
	</TD></TR>
	<TR><TH COLSPAN=2>
	<INPUT NAME="submit"		TYPE="submit"	value="Login" >
	<INPUT NAME="reset"		TYPE="reset"	value="Reset" >
	<INPUT NAME="clear"		TYPE="submit"	value="Clear" >
	<INPUT NAME="login_user"	TYPE="hidden"	value="1" >
	</TR></TABLE>
	</FORM>
</TH></TR>
<TR><TH>
	<FORM ACTION="create_user.php" METHOD="POST">
	<INPUT NAME="create_update_user" TYPE="submit"	value="Create User" >
	</FORM>
</TH></TR>
</TABLE>
</TH></TR>
</TABLE>
$q
</CENTER>
<P>
NOT_LOGGED_IN;

if ($phpinfo) { phpinfo(); }

print <<<NOT_LOGGED_IN
</BODY>
</HTML>
NOT_LOGGED_IN;

}

// if coming to reset login
	if (! $HTTP_POST_VARS["username"]) {
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
		exit;
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
		$user = New User($uid, $username,$access_level,$project,
			$db->bids($uid));
		print <<<PAGE
<HTML>
<BODY $result_bg>
<P>
<P>
<CENTER>
<H2>
<A HREF="indexF.php" TARGET="_parent"><BLINK>Click Here To Continue</BLINK></A>
</H2>
</CENTER>
</BODY>
</HTML>

PAGE;
		return;

	} else {
		not_logged_in("Password does not match at any level!");
	}

?>
