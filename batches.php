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
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

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
	card_head("{$user->project} - batches");
print "<!--{-->".form($_SERVER['PHP_SELF'],
	table(row(head($thelp))
	.row(head(
	"<!--{-->".table(row(cell("Update").cell($onebid->string_one_batch()))
	.row(head(
		input("submit","submit","Submit")
		.input("reset","reset","Reset")
		.input("submit","clear","Clear")
	,"colspan=2"))
	,"class=\"form\"")."<!--}-->\n"))
,"class=\"tight\"")."<!--}-->\n");
	showphpinfo();
	card_foot();
?>
