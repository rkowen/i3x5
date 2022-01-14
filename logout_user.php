<?php
// DESC: logs out the user and resets most values
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "cards.inc";
	if (isset($_POST["logout"])) {
		session_destroy();
		session_write_close();
		header("Location: index.php");
		return;
	}
	if (isset($_POST["back"])) {
		session_write_close();
		header("Location: indexM.php");
		return;
	}
$hlogout = sendhelp("{$_SESSION['user']->project} - Logout","logout");
card_head("{$_SESSION['user']->project} - Logout");
print <<<PAGE
$hlogout
<p>
PAGE;
print table(row(head(
	form($_SERVER['PHP_SELF'],
		table(row(head(
			input("submit","logout","Logout")."\n"
			.input("submit","back","Back")."\n"
		)),"class=\"form\"")
	)
)),"class=\"tight\"");

card_foot();

?>
