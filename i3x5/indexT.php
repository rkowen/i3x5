<?php 
	include_once "user.inc";
	include_once "cards.inc";

	session_start();
print <<<PAGE
<HTML>
<BODY $title_bg>

PAGE;
	print "<CENTER><B>".$user->project."</B></CENTER>\n";
	print "username: <B>{$user->uname}</B><BR>\n";

print <<<PAGE
<P>
<UL>
<LI><A HREF="login_user.php?login=Login+User" TARGET="main">
	Login New User</A>
<LI><A HREF="create_user.php" TARGET="main">Create New User</A>
<LI><A HREF="logout_user.php" TARGET="main">Logout User</A>
</UL>

</BODY>
</HTML>
PAGE;
?>
