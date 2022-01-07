<?php
// DESC: prompts to set the project/user encryption key
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	// can't get to this page unless $db->encode, but still assert
	if ($db->encode
	&&  isset($_POST["submit"])
	&&  strlen(clean($_POST["crypt"]))) {
// check if still active
		if (!isset($user,$user->uid)) {
			header("Location: login_user.php");
		}
		$_POST["crypt"] = clean($_POST["crypt"]);
// get DB object and service parameters
		$isprojcrypt = $db->sql(
"SELECT COUNT(1)
FROM	i3x5_userpass
WHERE	uid = {$user->uid}
AND	username = pgp_safe_decrypt(xusername, '{$_POST["crypt"]}')"
		);

		$result = "";
		if ($isprojcrypt) {
			$user->crypt = $_POST["crypt"];
			$user->encode = true;
			header("Location: index.php");
		} else {
			$result =
"Crypt key does not give valid encryption for user project.<br/>
Try again, or select action from left menus.";
			$user->encode = false;
		}
	}

$hprojcrypt = sendhelp("Crypt Key", "login crypt");
$hhintcrypt = sendhint("Hint", "Crypt");

card_head($_SESSION['user']->project." - Set Crypt Key");

print table(row(head(
	sendhelp($_SESSION['user']->project." - Set Crypt Key<br/>\n",
		"login crypt")
	.form($_SERVER['PHP_SELF'],
	table(	row(cell($hprojcrypt, "class=\"h_form\"")
		.cell(input("text","crypt","","size=35"))
		.cell("[".$hhintcrypt."]"))
		.row(head(
			input("submit","submit","Submit")."\n"
			.input("reset","reset","Reset")."\n"
		,"colspan=3"))
	,"class=\"form\"")
))),"class=\"tight\"");

if (strlen($result)) {
	print "<em class=\"warn\">$result</em>\n";
}

card_foot();

?>
