<?php
// DESC: List batch properties as a table
	include_once "cards.inc";
	include_once "user.inc";
	include_once "view.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";
	global $user;

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
	"count"	=> "Card<br/>Count",
	"rel"	=> "Related<br/>Cards",
	"crypt"	=> "Encrypt<br/>Cards"
	);
	$help = array(
	"bid"	=> "batch id",
	"name"	=> "batch name",
	"number"=> "batch number",
	"title"	=> "batch title",
	"card"	=> "batch card",
	"misc"	=> "batch relation",
	"count"	=> "batch count",
	"rel"	=> "batch related",
	"crypt"	=> "batch crypted",
	);

	function related_bid( $bid ) {
		global $db;

		$rbid = $db->sql("SELECT rid FROM i3x5_batch WHERE bid=$bid");

		if ($rbid) {
			return inform(" -> '"
				.$_SESSION['user']->bids[$rbid]["batch"]
				."' ($rbid)") ;
		}
		return;
	}

	// clear search cards
	if (isset($view->cards)) {
		unset($view->cards);
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
	$sp = "  ";
	foreach ($list as $k => $v) {
		$hv = sendhelp($v,$help[$k]);
		print $sp."<td class=\"h_form\">$hv</td>\n";
		$sp .= "  ";
	}
	print "</tr>\n";

// get batch card counts
	$bn = $db->bids_num($user->uid);
// put in totals
	$bk = count($bn) - 1;
	if ($bk >= 0) {
print	row(cell(sendhelp("TOTALS","batch totals"),
	"class=\"h_form\" id=\"right\" colspan=6")
	."\n   ".cell($bk["num"],"class=\"b_form\" id=\"right\"")
	."\n     ".cell($bk["numrid"],"class=\"b_form\" id=\"right\"")
	."\n       ".cell($bk["numcryp"],"class=\"b_form\" id=\"right\"")
	);
	}
// list batches owned by user
$oddeven = 0;
foreach ($_SESSION['user']->bids as $k => $v) {
	$oe = ($oddeven % 2 ? "b_form" : "b_form2");
	$oddeven += 1;
	$bk = $bn[$k];
	$fn = $db->batch_fieldnames($k);
print	row(cell(ahref("view_cards.php?view_edit=list&bid=$k",$k),
	"class=\"h_form\" id=\"right\"")
	."\n  ".cell(senddesc($v["batch"],$k,"batch"),"class=\"$oe\"")
	."\n    ".cell(senddesc($fn["num"],$k,"num"),"class=\"$oe\"")
	."\n      ".cell(senddesc($fn["title"],$k,"title"),"class=\"$oe\"")
	."\n        ".cell(senddesc($fn["card"],$k,"card"),"class=\"$oe\"")
	."\n          ".cell(related_bid($k),"class=\"$oe\"")
	."\n   ".cell($bk["num"],"class=\"$oe\" id=\"right\"")
	."\n     ".cell($bk["numrid"],"class=\"$oe\" id=\"right\"")
	."\n       ".cell($bk["numcryp"],"class=\"$oe\" id=\"right\"")
	);
}

print <<<PAGE
	</table><!--}-->
</th></tr>
</table><!--}-->
PAGE;
	showphpinfo();
	card_foot();
?>
