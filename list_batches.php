<?php
// DESC: List batch properties as a table
	include_once "cards.inc";
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";

	$insert = true;			// governs how data is input to table
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }
 
	// list of fields
	$list = array(
	"bid"	=> "Batch<br/>Id (bid)",
	"name"	=> "Batch<br/>Name",
	"number"=> "Number<br/>Field",
	"title"	=> "Title<br/>Field",
	"card"	=> "Card<br/>Field",
	"misc"	=> inform("Relation (bid)"),
	"count"	=> "Card<br/>Count"
	);
	$help = array(
	"bid"	=> "batch id",
	"name"	=> "batch name",
	"number"=> "batch number",
	"title"	=> "batch title",
	"card"	=> "batch card",
	"misc"	=> "batch relation",
	"count"	=> "batch count"
	);

	function related_bid( $bid ) {
		global $db;

		$rbid = $db->sql("SELECT rid FROM i3x5_batch WHERE bid=$bid");

		if ($rbid) {
			return inform(" -> '".$_SESSION['user']->bids[$rbid]["batch"]
				."' ($rbid)") ;
		}
		return;
	}

	function count_bid( $bid ) {
		global $db;

		$cnt = $db->sql(
			"SELECT COUNT(id) FROM i3x5_cards WHERE bid=$bid");

		return $cnt;
	}

	$hhead = sendhelp("{$_SESSION['user']->project} - List Batches", "list batches");
	card_head("{$_SESSION['user']->project} - List Batches");
print <<<PAGE
<!--{-->
<table class="tight">
<tr><th>$hhead</th></tr>
<tr><th>
	<!--{-->
	<table class="form" border=1>
PAGE;
	print "<tr>\n";
	reset($list);
	$sp = "  ";
	while(list($k,$v) = each($list)) {
		$hv = sendhelp($v,$help[$k]);
		print $sp."<td class=\"h_form\">$hv</td>\n";
		$sp .= "  ";
	}
	print "</tr>\n";

// list batches owned by user
reset($_SESSION['user']->bids);
while (list($k,$v) = each($_SESSION['user']->bids)) {
	$fn = $db->batch_fieldnames($k);
print	row(cell($k,"class=\"h_form\" id=\"right\"")
	."\n  ".cell(senddesc($v["batch"],$k,"batch"),"class=\"b_form\"")
	."\n    ".cell(senddesc($fn["num"],$k,"num"),"class=\"b_form\"")
	."\n      ".cell(senddesc($fn["title"],$k,"title"),"class=\"b_form\"")
	."\n        ".cell(senddesc($fn["card"],$k,"card"),"class=\"b_form\"")
	."\n          ".cell(related_bid($k),"class=\"b_form\"")
	."\n            ".cell(count_bid($k),"class=\"b_form\" id=\"right\""));
}

print <<<PAGE
	</table><!--}-->
</th></tr>
</table><!--}-->
PAGE;
	showphpinfo();
	card_foot();
?>
