<?php
// DESC: search the project help for keywords and show the entries.
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

$db = new i3x5_DB($schema);

if (isset($_POST["clear"])) {
	$_POST["keywords"] = "";
}
if (isset($_POST["search"])) {
	$help = $db->helpsearch($_POST["keywords"]);
} else {
	$_POST["keywords"] = "";
	$help = NULL;
}

$helpsearch = sendhelp("Keyword(s)", "search help");

card_head($_SESSION['user']->project." - Search Help");

print table(row(head(
	sendhelp($_SESSION['user']->project." - Search Help<br/>\n",
		"search help")
	.form($_SERVER['PHP_SELF'],
	table(	row(cell($helpsearch, "class=\"h_form\"")
		.cell(input("text","keywords",$_POST["keywords"],"size=35")))
		.row( 	head(
			input("submit","search","Search")."\n"
			.input("submit","clear","Clear")."\n"
		,"colspan=2"))
	,"class=\"form\"")
))),"class=\"tight\"");

if (isset($help)) {
print <<<THEAD
<table class="cardview">
THEAD;
	print row(head("Key").head("Text"))."\n";
	$oddeven = 0;
	foreach($help as $k => $v) {
		$oe = ($oddeven %2 ? "b_form" : "b_form2");
		$oddeven += 1;
		$khelp = sendhelp($k,$k);
		print row(cell($khelp,"class=\"h_title\"").cell($v["help"],"class=\"$oe\""))."\n";

	}
print <<<TEND
</table>
TEND;
}

card_foot();
showphpinfo();

?>
