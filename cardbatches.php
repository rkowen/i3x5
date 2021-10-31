<?php
// DESC: Show the given card's list of batches
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	if (isset($_GET["cid"])) {
		$cid = $_GET["cid"];

	include_once "3x5_db.inc";
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

print <<<PAGE
<!--{-->
<table class="ajaxbatch">
PAGE;

$cb = $db->card_batches($user->uid,$cid);
reset($cb);

$maincard = "";
$relcard  = "";

while (list($k,$v) = each($cb)) {
	$x = cell($v["batch"]).cell("(".$v["bid"].")","style=\"text-align: right;\"");
	if ($v["basecard"] == "t") {
		$y = "style=\"bgcolor: $related_warn;\"";
		$maincard = row(
			cell(ahref(
"search_batches.php?view_body=header&cidbatch=$k",$k,
			"class=\"relcard\""),
			"style=\"text-align: right;\"").$x,
			"style=\"background-color: $related_warn;\"")."\n";
	} else {
		$y = "class=\"relate_color\"";
		$relcard = $relcard . row(
			cell($k,"style=\"text-align: right;\"").$x,
			"style=\"background-color: $relate_color;\"")."\n";
	}
}
print row(head(sendhelp("cid","cid"))
	.head(sendhelp("batch","batch name"))
	.head(sendhelp("bid","bid")))."\n";
print $maincard;
print $relcard;
print <<<PAGE
</table>
<!--}-->
PAGE;
	}

?>
