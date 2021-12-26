<?php
// DESC: starting "results" page
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	card_head("Main Frame");

	if (isset($user)) {
		$db = new i3x5_DB($schema);
		print <<<PAGE
<h2>"{$user->project}" - results</h2>
This the "{$user->project}" results frame
<p>
Click any of the links in the left frames and the subsequent
actions will be shown in this frame.
</p>
PAGE;
		if ($db->encode && !strlen($user->crypt)) {
	$invalid = ahref("crypt_user.php","invalid","target=\"main\"");
			print <<<NOCRYPT
<p>
The crypt key given is $invalid (click here to set).
</p>
NOCRYPT;
		}
	} else {
	print <<<PAGE
<h2>Please Login</h2>
<p>
You are not currently logged in.
</p>
<p>
Please click
<a href="login_user.php" target="main">here to login</a>
if you have an existing project.
</p>
<p>
Or click
<a href="create_user.php" target="main">here to create a new user</A>.
</p>

PAGE;
	}
	showphpinfo();

	card_foot();
?>
