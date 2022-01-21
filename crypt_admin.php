<?php
// DESC: prompts to set the project/user encryption key
	include_once "user.inc";
	include_once "common.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$result = "";
	$db = new i3x5_DB($schema);
	// can't get to this page unless $db->encode, but still assert
	if ($db->encode
	&&  isset($_POST["submit"])) {
// check if still active
		if (!isset($user,$user->uid)) {
			header("Location: login_user.php");
		}
		$_POST["newcrypt"] = clean($_POST["newcrypt"]);
		$_POST["numxcard"] = clean($_POST["numxcard"]);
		if ($_POST["numxcard"] > 0) {
			$_POST["oldcrypt"] = clean($_POST["oldcrypt"]);
		}
		$_POST["hint"] = clean($_POST["hint"]);
// set xusername
		$setxumsg = $db->sql(
"UPDATE	i3x5_userpass
SET	crypthint = '{$_POST["hint"]}'".
(strlen($_POST["newcrypt"])
?	",xusername = pgp_sym_encrypt(username,'{$_POST["newcrypt"]}')"
:	"")
."
WHERE	uid = {$user->uid}");

// reencrypt any cards
		$numxcard = $db->count_xcards($user->uid);
		if ($numxcard > 0) {
			$resetxcard = $db->convert_xcards($user->uid,
				$_POST["oldcrypt"],$_POST["newcrypt"]);
		}
// reload project
		if (strlen($_POST["newcrypt"])) {
			$user->crypt = $_POST["newcrypt"];
			$user->encode = true;
		}
		header("Location: index.php");
	}

$hprojoldcrypt = sendhelp("Old Crypt Key", "projcrypt");
$hprojnewcrypt = sendhelp("New Crypt Key", "projcrypt");
$hhintcrypt = sendhint("Hint", "projcrypthint");

card_head($_SESSION['user']->project." - Set Crypt Key");

$numxcard = $db->count_xcards($user->uid);
// don't need old crypt key if no cards are encrypted

print table(row(head(
	sendhelp($_SESSION['user']->project." - Admin Set Crypt Key<br/>\n",
		"login crypt")
	.form($_SERVER['PHP_SELF'],
	table(
($numxcard > 0
?		row(cell($hprojoldcrypt, "class=\"h_form\"")
		.cell(input("text","oldcrypt","","size=35")))."\n"
: "")
		.row(cell($hprojnewcrypt, "class=\"h_form\"")
		.cell(input("text","newcrypt","","size=35")))."\n"
		.row(cell($hhintcrypt, "class=\"h_form\"")
		.cell(input("text","hint","","size=35")))."\n"
		.row(head(
			input("submit","submit","Submit")."\n"
			.input("reset","reset","Reset")."\n"
			.input("hidden","numxcard","$numxcard")."\n"
		,"colspan=2"))."\n"
	,"class=\"form\"")
))
.row(head("There are $numxcard encrypted cards to be converted."))
),"class=\"tight\""
);

if (strlen($result)) {
	print "<em class=\"warn\">$result</em>\n";
}

showphpinfo();
card_foot();

?>
