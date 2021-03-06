<?php
// DESC: starting ``results'' page
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";

	card_head("Main Frame");

	if (isset($user)) {
	print <<<PAGE
<H2>``{$user->project}'' - results</H2>
This the ``{$user->project}'' results frame
<P>
Click any of the links in the left frames and the subsequent
actions will be shown in this frame.

<P>
PAGE;
	} else {
	print <<<PAGE
<H2>Please Login</H2>
<P>
You are not currently logged in.
<P>
Please click
<A HREF="login_user.php" TARGET="main">here to login</A>
if you have an existing project.
<P>
Or click
<A HREF="create_user.php" TARGET="main">here to create a new user</A>.
<P>

PAGE;
	}
	showphpinfo();

	card_foot();
?>
