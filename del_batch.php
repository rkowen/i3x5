<?php
// DESC: Delete a whole batch from the DB
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
if (isset($one_batch__) && $one_batch__) {
	$rid = $db->sql(
	"SELECT count(*) FROM i3x5_batch WHERE rid=$one_batch__");
	if ($rid) {
		$msg = "Batch {$user->bids[$one_batch__]["batch"]} is "
		."linked to from $rid other batch".($rid==1?"":"es");
		$one_batch__ = false;
	}
}

if (isset($do_delete) && $delete=="Delete") {

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
	card_head("{$user->project} - Delete Batch");

if (isset($one_batch__) && $one_batch__) {
	$hbatch = senddesc("{$user->bids[$one_batch__]["batch"]}",
		$one_batch__,"batch");
	print form($_SERVER['PHP_SELF'],
	"<!--{-->".table(row(head($hdel))
		.row(head("<!--{-->".table(
			row(head(warn(
"All non-linked cards<br/>will be deleted with batch")))
			.(isset($msg)?row(head(warn($msg))):"")
			.row(head($hbatch))
			.row(head(
				input("submit","delete","Delete")
				.input("submit","delete","Skip")
				.input("hidden","do_delete",$one_batch__)))
		,"class=\"form\"")."<!--}-->\n"))."\n"
	,"class=\"tight\"")."<!--}-->\n")."\n";
} else {
	print form($_SERVER['PHP_SELF'],
	"<!--{-->".table(row(head($hdel))
		.row(head("<!--{-->".table(
			row(cell("Delete ").cell($onebid->string_one_batch()))
			.row(head(
				input("submit","delete","Delete")
				.input("reset","reset","Reset"),"colspan=2"))
		,"class=\"form\"")."<!--}-->\n"))."\n"
		.(isset($msg)?row(head(inform($msg))):"")
	,"class=\"tight\"")."<!--}-->\n")."\n";
}
	showphpinfo();
	card_foot();
?>
