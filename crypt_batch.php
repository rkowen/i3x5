<?php
// DESC: Encrypt/Decrypt a whole batch from the DB
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

	if (!isset($user,$user->uid)) {
		header("Location: login_user.php");
	}

	if (isset($_POST["encrypt"])) {
		$msg = $db->encrypt_batch($one_batch__, $user->crypt);
		$crypt = "TRUE";
		if (!strlen($msg)) {
			$msg = "Batch Encrypted";
		}
	} elseif (isset($_POST["decrypt"])) {
		$msg = $db->decrypt_batch($one_batch__, $user->crypt);
		$crypt = "FALSE";
		if (!strlen($msg)) {
			$msg = "Batch Decrypted";
		}
	}
	if (isset($_GET["msg"])) {
		$msg = $_GET["msg"];
	}

// set or unset batch encrypted bit
	if (isset($_POST["encrypt"]) || isset($_POST["decrypt"])) {
		$db->sql(
"UPDATE	i3x5_batch
SET	encrypted = $crypt
WHERE	bid = $one_batch__");
	}

	$hcrypt = sendhelp(
		"{$user->project} - Encrypt/Decrypt Batch","crypt batch");
	card_head("{$user->project} - Encrypt/Decrypt Batch");

	print form($_SERVER['PHP_SELF'],
	"<!--{-->".table(row(head($hcrypt))
		.row(head("<!--{-->".table(
			row(cell("Cryption ").cell($onebid->string_one_batch()))
			.row(head(
				input("submit","encrypt","Encrypt")
				.input("submit","decrypt","Decrypt")
				.input("reset","reset","Reset"),"colspan=2"))
		,"class=\"form\"")."<!--}-->\n"))."\n"
		.(isset($msg)?row(head(inform($msg))):"")
	,"class=\"tight\"")."<!--}-->\n")."\n";

	showphpinfo();
	card_foot();
?>
