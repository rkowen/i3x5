<?php
// DESC: select batch operations

	include_once "cards.inc";
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";
	include_once "one_batch.inc";

	$onebid = new OneBatch();
	$one_batch__ = $onebid->get_one_batch();

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	$url = "new_batch.php?batch_select";
	if ( isset($_POST["submit"] )) {
		$bname = $db->one_row(
"SELECT batch,batch_help FROM i3x5_batch ".
"WHERE uid={$user->uid} AND bid='$one_batch__'");
		header("Location: $url=Update&bid=$one_batch__&name=".
			urlencode($bname['batch'])
			."&name_help=".urlencode($bname['batch_help']));
		return;
	}

	$thelp = sendhelp("{$user->project} - batch properties",
		"batch properties");
	print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - batches</TITLE>
<BODY $result_bg>
<CENTER>
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>$thelp</TH></TR>
<TR><TH>
	<FORM ACTION="{$_SERVER['PHP_SELF']}" METHOD="POST">
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TD>Update</TD><TD>

PAGE;
	$onebid->show_one_batch();

	print <<<PAGE
		</SELECT>
	</TD></TR>
	<TR><TH COLSPAN=4>
	<INPUT NAME="submit"			TYPE="submit"	value="Submit" >
	<INPUT NAME="reset"			TYPE="reset"	value="Reset" >
	<INPUT NAME="clear"			TYPE="submit"	value="Clear" >
	</TD></TR>
	</FORM>
	</TABLE> <!--}-->
</TH></TR>
</TABLE> <!--}-->
</CENTER>
PAGE;
	if ($phpinfo) {phpinfo();}
	print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
