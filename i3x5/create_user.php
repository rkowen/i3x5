<?php
	session_start();

	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	include "create_update_user.inc";
	$cuu = new Create_Update_User("Create");

	$cuu->get_form();

	if (ereg("Done", $HTTP_POST_VARS["create_update_user"])) {
// it looks like have valid input ... now ship it off to the DB
		$db->sql(
"INSERT INTO i3x5_userpass (username,passwd_admin,passwd_w,passwd_a,passwd_r,".
"author,email,challenge,response) VALUES ('".
$HTTP_POST_VARS["username"]."','".
$HTTP_POST_VARS["passwd_admin"]."','".
$HTTP_POST_VARS["passwd_w"]."','".
$HTTP_POST_VARS["passwd_a"]."','".
$HTTP_POST_VARS["passwd_r"]."','".
$HTTP_POST_VARS["author"]."','".
$HTTP_POST_VARS["email"]."','".
$HTTP_POST_VARS["challenge"]."','".
$HTTP_POST_VARS["response"]."')"
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
