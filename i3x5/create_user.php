<?php
// DESC: Create a new user and insert into DB
	session_start();

	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	include "create_update_user.inc";
	$cuu = new Create_Update_User("Create");

	$cuu->get_form();

	if (ereg("Done", $_POST["create_update_user"])) {
// it looks like have valid input ... now ship it off to the DB
		$db->sql(
"INSERT INTO i3x5_userpass (username,passwd_admin,passwd_w,passwd_a,passwd_r,".
"author,email,challenge,response) VALUES ('".
$_POST["username"]."','".
$_POST["passwd_admin"]."','".
$_POST["passwd_w"]."','".
$_POST["passwd_a"]."','".
$_POST["passwd_r"]."','".
$_POST["author"]."','".
$_POST["email"]."','".
$_POST["challenge"]."','".
$_POST["response"]."')"
		);

// have set-up a new user ... now roll over to login_user which will
// validate and pass off to frames
		header("Location: login_user.php");
		return;
	} else {

	$cuu->show_form("Create User", "create user");

print <<<NOT_CREATED
</CENTER>
<P>
<P>
Please be sure to give a valid email address.  This is how the
``<B>3x5</B>'' project
will notify you if you forget your admin password, or if your project
will be deleted for extended inactivity.
<BR>
Please remember the <EM>admin password</EM> ... this is the most important
password you need, the other passwords are to grant varying levels
of access to other members of your organization or project.
<P>
As a courtesy, give a valid name too.  None of this information will be
used or given out (except to law officers if directed by legal authorities).
<P>
Your project may be deleted at any time if they are found to contain
illegal or offensive material.  However, the
``<B>3x5</B>'' project, is not responsible for the content therein
and can not guarentee the absolute confidentiality of the content either.
<P>
In other words, do not put anything into the 
``<B>3x5</B>'' project that is private, confidential,
illegal, or sensitive.
</BODY>
</HTML>
NOT_CREATED;
		if ($phpinfo) { phpinfo(); }
	}
?>
