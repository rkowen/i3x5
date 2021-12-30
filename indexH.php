<?php
// DESC: help frame with information when clicked upon elsewhere
	include_once "user.inc";
	include_once "common.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	if (isset($_GET["bid"])) {
		$bid = $_GET["bid"];
	}
	if (isset($_GET["property"])) {
		$property = $_GET["property"];
	}
	if (isset($_GET["help"])) {
		$help = $_GET["help"];
	}
	if (isset($_GET["hint"])) {
		$hint = $_GET["hint"];
	}
	card_head("Help Frame","helptext",0);

	$db = new i3x5_DB($common->schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

	global $user;
	if (isset($help)) {
		print "<h4>Help</h4>\n";
		print "<p class=\"help\">".help($db->helpmsg($help))."</p>\n";
	} elseif (isset($bid) && isset($user, $user->uid)) {
		print "<h4>Project Help</h4>\n";
		print "<p class=\"help\">"
			.help($db->helpdesc($bid,$property))."</p>\n";
	} elseif (isset($hint) && isset($user, $user->uid)) {
		print "<h4>Project $hint Hint</h4>\n";
		print "<p class=\"help\">"
			.help($db->helphint($user->uid,$hint))."</p>\n";
	} else {
		print "<h4>Project Disconnected</h4>\n";
		print ahref("login_user.php?login=Login+User",
			"Login",
			"target=\"main\"");
	}
?>
