<?php
// DESC: logs out the user and resets most values
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "cards.inc";
	if (isset($_POST["logout"])) {
		session_destroy();
		session_write_close();
		header("Location: indexM.php");
		return;
	}
	if (isset($_POST["back"])) {
		session_write_close();
		header("Location: indexM.php");
		return;
	}
	$hlogout = sendhelp("{$_SESSION['user']->project} - Logout","logout");
print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$_SESSION['user']->project} - Logout </TITLE>
</HEAD>
<BODY $result_bg>
<CENTER>
$hlogout
<P>
<TABLE BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BCOLOR="$box_color">
<TR><TH>
	<FORM ACTION="{$_SERVER['PHP_SELF']}" METHOD="POST">
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TH>
	<INPUT NAME="logout"		TYPE="submit"	value="Logout" >
	<INPUT NAME="back"		TYPE="submit"	value="Back" >
	</TH></TR></TABLE>
	</FORM>
</TH></TR>
</TABLE>
</TH></TR>
</TABLE>
</CENTER>

<P>
</BODY>
</HTML>
PAGE;

?>
