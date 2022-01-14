<?php
// DESC: Create a new user and insert into DB
	session_start();

	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

	include "create_update_user.inc";
	$cuu = new Create_Update_User("Create");

	$cuu->get_form();

	if (preg_match("/Done/", $_POST["create_update_user"])) {
// it looks like have valid input ... now ship it off to the DB
if ($db->encode) {
		$x = $db->crypt;
		$y = clean($_POST["projcrypt"]);
		$db->sql(
"INSERT INTO i3x5_userpass (
	username,
	xusername,
	xpasswd_admin,
	xpasswd_w,
	xpasswd_a,
	xpasswd_r,
	author,
	email,
	challenge,
	response,
	crypthint
) VALUES (
	'".$db->escape(clean($_POST["username"]))."',
	pgp_sym_encrypt('".clean($_POST["username"])."','$y'),
	pgp_sym_encrypt('".clean($_POST["passwd_admin"])."','$x'),
	pgp_sym_encrypt('".clean($_POST["passwd_w"])."','$x'),
	pgp_sym_encrypt('".clean($_POST["passwd_a"])."','$x'),
	pgp_sym_encrypt('".clean($_POST["passwd_r"])."','$x'),
	'".$db->escape(clean($_POST["author"]))."',
	'".$db->escape(clean($_POST["email"]))."',
	'".$db->escape(clean($_POST["challenge"]))."',
	'".$db->escape(clean($_POST["response"]))."',
	'".$db->escape(clean($_POST["projcrypthint"]))."'
)"
		);
} else {
		$db->sql(
"INSERT INTO i3x5_userpass (
	username,
	passwd_admin,
	passwd_w,
	passwd_a,
	passwd_r,
	author,
	email,
	challenge,
	response
) VALUES (
	'".$db->escape(clean($_POST["username"]))."',
	'".$db->escape(clean($_POST["passwd_admin"]))."',
	'".$db->escape(clean($_POST["passwd_w"]))."',
	'".$db->escape(clean($_POST["passwd_a"]))."',
	'".$db->escape(clean($_POST["passwd_r"]))."',
	'".$db->escape(clean($_POST["author"]))."',
	'".$db->escape(clean($_POST["email"]))."',
	'".$db->escape(clean($_POST["challenge"]))."',
	'".$db->escape(clean($_POST["response"]))."'
)"
		);
}

// have set-up a new user ... now roll over to login_user which will
// validate and pass off to frames
		header("Location: login_user.php");
		return;
	} else {

	$cuu->show_form("Create User Project", "create user");

print <<<NOT_CREATED
</center>
<p></p>
<p>
Please be sure to give a valid email address.  This is how the
"<b>3x5</b>" project
will notify you if you forget your admin password, or if your project
will be deleted for extended inactivity.
<br/>
Please remember the <EM>admin password</EM> ... this is the most important
password you need, the other passwords are to grant varying levels
of access to other members of your organization or project.
</p><p>
As a courtesy, give a valid name too.  None of this information will be
used or given out (except to law officers if directed by legal authorities).
</p><p>
Your project may be deleted at any time if they are found to contain
illegal or offensive material.  However, the
"<b>3x5</b>" project, is not responsible for the content therein
and can not guarentee the absolute confidentiality of the content either.
</p><p>
In other words, do not put anything into the 
"<b>3x5</b>" project that is private, confidential,
illegal, or sensitive.
</p>
</body>
</html>
NOT_CREATED;
		showphpinfo();
	}
?>
