<?php
	session_start();

	include_once "cards.inc";

	if ($user) {
		$what = "indexM.php";
	} else {
		$what = "login_user.php";
	}
	print <<<PAGE
<HTML>
<HEAD>
<TITLE>3x5 Cards Project</TITLE>
</HEAD>
<FRAMESET COLS="200, 100%">
	<FRAMESET ROWS="120, 100%">
		<FRAME NAME="title" SRC="indexT.php">
		<FRAME NAME="options" SRC="indexB.php">
	</FRAMESET>
	<FRAME NAME="main" SRC="$what">
</FRAMESET>
<NOFRAME>
<BODY> You need to have a Frames capable browser!
</BODY>
</NOFRAME>
</HTML>
PAGE;
?>
