<?php
	include_once "user.inc";

	session_start();

	include_once "cards.inc";
	if ($HTTP_POST_VARS["logout"]) {
		session_destroy();
		header("Location: indexM.php");
		return;
	}
	if ($HTTP_POST_VARS["back"]) {
		header("Location: indexM.php");
		return;
	}
	$hlogout = sendhelp("{$user->project} - Logout","logout");
print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - Logout </TITLE>
</HEAD>
<BODY $result_bg>
<CENTER>
$hlogout
<P>
<TABLE BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BCOLOR="$box_color">
<TR><TH>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
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
