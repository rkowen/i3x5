<?php
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	session_register("view");
	include_once "cards.inc";
	include_once "many_batch.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	$manybid = new ManyBatch();
	$manybid->get_many_batch();
	
if ($view) {
	$view->get_buttons();
} else {
	$view = new View();
}
// process batches if given view go-ahead
if ($HTTP_POST_VARS["submit"] == "View") {
	if ( (! is_array($HTTP_POST_VARS["many_batch__"]))
	|| (count($HTTP_POST_VARS["many_batch__"]) == 0)) {
		$errmsg = "Nothing to view! Select some batches.";
	} else {
		$manybid->set_user_selected();
		header("Location: view_cards.php");
		return;
	}
}

$hhead = sendhelp("{$user->project} - Select Batches", "batch select");
	print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - Select Batches</TITLE>
<BODY $result_bg>
<CENTER>
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH> $hhead </TH></TR>

PAGE;
	if ($errmsg) {
		print "<TR><TH>".warn($errmsg)."</TH></TR>\n";
	}
	print <<<PAGE
<TR><TH>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<!--{-->
	<TABLE WIDTH=100% BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TH VALIGN="top">
PAGE;
	$manybid->show_many_batch();
	print <<<PAGE

	</TH></TR>
PAGE;
	print "<TR><TH>\n";
	$view->show_buttons();
	print "</TH></TR>\n";
	print <<<PAGE
	<TR><TH>
	<INPUT NAME="submit"	TYPE="submit"	value="View" >
	<INPUT NAME="submit"	TYPE="submit"	value="Check" >
	<INPUT NAME="reset"	TYPE="reset"	value="Reset" >
	<INPUT NAME="clear"	TYPE="submit"	value="Clear" >
	</TH></TR>
	</FORM>
	</TABLE>
	<!--}-->
</TH></TR>
</TABLE>
<!--}-->
</CENTER>
PAGE;
	if ($phpinfo) {phpinfo();}
	print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
