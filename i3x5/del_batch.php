<?php
// DESC: Delete a whole batch from the DB
	include_once "cards.inc";
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

if (isset($_POST["del_batch"])) {
	$del_batch = $_POST["del_batch"];
}
if (isset($_POST["delete"])) {
	$delete = $_POST["delete"];
}
if (isset($_POST["do_delete"])) {
	$do_delete = $_POST["do_delete"];
}
if (isset($_GET["msg"])) {
	$msg = $_GET["msg"];
}
// check if batch properties are linked to
if ($del_batch) {
	$rid = $db->sql(
	"SELECT count(*) FROM i3x5_batch WHERE rid=$del_batch");
	if ($rid) {
		$msg = "Batch {$user->bids[$del_batch]["batch"]} is "
		."linked to from $rid other batch".($rid==1?"":"es");
		$del_batch = false;
	}
}

if ($do_delete && $delete=="Delete") {

// delete non-related cards
	$db->sql(
"DELETE FROM i3x5_cards WHERE bid=$do_delete\n".
"AND id IN (\n".
"SELECT id FROM i3x5_cards\n".
"WHERE bid = $do_delete\n".
"EXCEPT\n".
"SELECT rid AS id FROM i3x5_cards\n".
"WHERE bid != $do_delete AND rid IS NOT NULL)");

// for now this only deletes the batch entry ... not the cards
	$c = $db->sql("SELECT COUNT(*) FROM i3x5_cards WHERE bid=$do_delete");
	if ($c == 0) {
		$db->sql("DELETE FROM i3x5_batch WHERE bid=$do_delete");
		$url = "del_batch.php?msg="
		.urlencode("Deleted Batch {$user->bids[$do_delete]["batch"]}");
		// need to update list of bids (after deleting one)
		$user->update_bids($db->bids($user->uid));
	} else {
		$url = "del_batch.php?msg="
		.urlencode("Batch {$user->bids[$do_delete]["batch"]} has "
		."$c linked cards");
	}
	header("Location: $url");
	return;
}

	$hdel = sendhelp("{$user->project} - Delete Batch","delete batch");
	print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - Delete Batch</TITLE>
<BODY $result_bg>
<CENTER>
PAGE;

if ($del_batch) {
	print <<<PAGE
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>$hdel</TH></TR>
<TR><TH>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TH>
PAGE;
	print warn("All non-linked cards<BR>will be deleted with batch")
		."</TH></TR>\n";
	if ($msg) {
		print row(cell(warn($msg)))."\n";
	}
	$hbatch = senddesc("{$user->bids[$del_batch]["batch"]}",
		$del_batch,"batch");
	print <<<PAGE
	<TR><TH>
	<BIG>$hbatch</BIG>
	</TH></TR>
	<TR><TH>
	<INPUT NAME="delete"		TYPE="submit"	value="Delete" >
	<INPUT NAME="delete"		TYPE="submit"	value="Skip" >
	<INPUT NAME="do_delete"		TYPE="hidden"	value="$del_batch" >
	</TH></TR>
	</TABLE> <!--}-->
	</FORM>
</TH></TR>
</TABLE> <!--}-->
PAGE;

} else { 
	print <<<PAGE
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH>$hdel</TH></TR>
<TR><TH>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
PAGE;
	if ($msg) {
		print row(cell(warn("<BIG>$msg</BIG>"),
			"ALIGN=CENTER"))."\n";
	}
	print <<<PAGE
	<TR><TH>
	<SELECT NAME="del_batch" SIZE=1>
PAGE;
	// list batches owned by user
	reset($user->bids);
	while (list($k,$v) = each($user->bids)) {
		$sel_ = (($del_batch == $k) ? "SELECTED" : "" );
		print "<OPTION ".$sel_." VALUE=\"$k\">{$v["batch"]}</OPTION>\n";
	}

	print <<<PAGE
		</SELECT>
	</TH></TR>
	<TR><TH>
	<INPUT NAME="delete"			TYPE="submit"	value="Delete" >
	<INPUT NAME="reset"			TYPE="reset"	value="Reset" >
	</TH></TR>
	</TABLE> <!--}-->
	</FORM>
</TH></TR>
</TABLE> <!--}-->
PAGE;
}
	print <<<PAGE
</CENTER>
PAGE;
	if ($phpinfo) {phpinfo();}
	print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
