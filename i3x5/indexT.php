<?php 
// DESC: title frame, shows username
	include_once "user.inc";
	include_once "cards.inc";

	session_start();
	include_once "session.inc";

print <<<PAGE
<HTML>
<BODY $title_bg>

PAGE;
	print "<CENTER><B>".$user->project."</B></CENTER>\n";
	print "username: <B>{$user->uname}</B><BR>\n";

print <<<PAGE
<P>
<UL>
PAGE;
if (!$user) {
print <<<PAGE
<LI><A HREF="login_user.php?login=Login+User" TARGET="main">
	Login New User</A>
<LI><A HREF="create_user.php" TARGET="main">Create New User</A>
PAGE;
} else {
print <<<PAGE
<LI><A HREF="logout_user.php" TARGET="main">Logout User</A>
PAGE;
}
print <<<PAGE
</UL>

</BODY>
</HTML>
PAGE;
?>
