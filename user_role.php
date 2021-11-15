<?php
// DESC: For the admin - change the current access level for test purposes
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$debug = 0;

	if (isset($_GET["errmsg"])) {
		$errmsg = $_GET["errmsg"];
	}
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

// process batches if given view go-ahead
if (isset($_POST["submit"]) && ($_POST["submit"] == "Submit")) {
	if (! isset($_POST["level"])) {
		$errmsg = "Nothing set! Select a level.";
	} else {
		$x_lvl = "level_".$_POST["level"];
		$lvl = ${$x_lvl};
		if (isset($lvl)) {
			$user->level = $lvl;
			session_write_close();
if (!$debug) {
			header("Location: index.php");
}
		} else {
			card_head("{$user->project} - Select Access");
			print <<<PAGE
<h2>
Pick a "Select From Access" operation in left menu<br/>
</h2>
</center>
PAGE;
			showphpinfo();
			print "</body>\n";
			return;
		}
	}
}

function cul($lvl) {
	global $user;
	global $level_admin,$level_write,$level_append,$level_read;
	$x_lvl = "level_{$lvl}";
	$nlvl = ${$x_lvl};
	if ($nlvl == $user->level) {
		return input("radio","level",$lvl,"checked");
	} else {
		return input("radio","level",$lvl);
	}
}
function hul($key,$lbl) {
	return label($key,sendhelp($lbl,$lbl));
}
function sul($level,$text) {
	global $user;
	// show only if level <= $user->reallevel
	if ($level <= $user->reallevel) {
		return $text;
	} else {
		return "";
	}
}

$hhead = sendhelp("{$user->project} - Change Access Level", "change role");
	card_head("{$user->project} - Change Access Level");
	print form($_SERVER['PHP_SELF'],
	"<!--{-->".table(row(head($hhead))
		.(isset($errmsg)?row(head(warn($errmsg)))."\n":"")
		.row(head("<!--{-->".table(
	sul($level_admin,
			row(cell(cul("admin"), "style=\"text-align:right;\"")
				.cell(hul("admin","Admin"))))
	.sul($level_write,
			row(cell(cul("write"), "style=\"text-align:right;\"")
				.cell(hul("write","Write"))))
	.sul($level_append,
			row(cell(cul("append"), "style=\"text-align:right;\"")
				.cell(hul("append","Append-Only"))))
			.row(cell(cul("read"), "style=\"text-align:right;\"")
				.cell(hul("read","Read-Only")))
			.row(head(
				input("submit","submit","Submit")
				.input("reset","reset","Reset"),
				"colspan=\"2\""
			)),"style=\"width: 100%;\"")."<!--}-->"
			))
	,"class=\"tight\"")."<!--}-->")."\n";
	showphpinfo();
if ($debug) {
print("<br/><bold>results</bold><br/>\n");
print("<br/><bold>$sql</bold><br/>\n");
print("<br/><bold>$errmsg</bold><br/>\n");
print("<pre style=\"text-align:left;\">view=\n");
print_r($_POST);
print_r($user);
print("</pre>\n");
}
	card_foot();
?>
