<?php
// DESC: starting "results" page
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";

	card_head("Main Frame");

	if (isset($user)) {
	print <<<PAGE
<h2>"{$user->project}" - results</h2>
This the "{$user->project}" results frame
<p>
Click any of the links in the left frames and the subsequent
actions will be shown in this frame.
</p>
PAGE;
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
