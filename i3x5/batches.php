<?php

	include_once "cards.inc";
	include_once "user.inc";

	session_start();

	include_once "3x5_db.inc";
	include_once "one_batch.inc";

	$onebid = new OneBatch();
	$one_batch__ = $onebid->get_one_batch();

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

// see if we've are creating a new batch
	$new_batch = trim($new_batch);
	$url = "new_batch.php?batch_select";
	if ( $batch_select == "New" ) {
		header("Location: $url=New&example=card&name=".
			urlencode($new_batch));
		return;
	} elseif ( $batch_select == "Update" ) {
		$bname = $db->one_row(
"SELECT batch,batch_help FROM i3x5_batch ".
"WHERE uid={$user->uid} AND bid='$one_batch__'");
		header("Location: $url=Update&bid=$one_batch__&name=".
			urlencode($bname[batch])
			."&name_help=".urlencode($bname[batch_help]));
		return;
	}

// see which radio button should be CHECKED as to type
	if (! $b_select ) {
		$batch_select = "Update";
	} else {
		$batch_select = $b_select;
	}
	$check = "Check_".$batch_select;
	$$check = "CHECKED";

if (0) {
	print "b_select = ".$b_select."<BR>\n";
	print "batch_select = ".$batch_select."<BR>\n";
	print "new_batch = ".$new_batch."<BR>\n";
	print "one_batch__ = ".$one_batch__."<BR>\n";
	print "uid = ".$user->uid."<BR>\n";
	print "onebid : \n";
		$onebid->dump();

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
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TH>
	<INPUT NAME="batch_select" $Check_New TYPE="radio" VALUE="New">
	</TH><TD>New</TD><TD>
	<INPUT NAME="new_batch" SIZE=25 MAXLENGTH=50 TYPE="text" VALUE="$new_batch">
	</TD>$new_batch_error</TR>
	<TR><TH>
	<INPUT NAME="batch_select" $Check_Update TYPE="radio" VALUE="Update">
	</TH><TD>Update</TD><TD COLSPAN=2>

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
