<?php
// DESC: List batch properties as a table
	include_once "cards.inc";
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";

	$insert = true;			// governs how data is input to table
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }
 
	// list of fields
	$list = array(
	"bid"	=> "Batch<BR>Id",
	"name"	=> "Batch<BR>Name",
	"number"=> "Number<BR>Field",
	"title"	=> "Title<BR>Field",
	"card"	=> "Card<BR>Field",
	"misc"	=> inform("Relation"),
	"count"	=> "Card<BR>Count"
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
			return inform("Related to '".$_SESSION['user']->bids[$rbid]["batch"]
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
print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$_SESSION['user']->project} - List Batches</TITLE>
<BODY $result_bg>
<CENTER>
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH><A HREF="indexM.php"><IMG SRC="back.gif" ALT="back" BORDER="0" ></A>
</TH><TH>$hhead</TD></TR>
<TR><TH COLSPAN=2>
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
PAGE;
	print "<TR>\n";
	reset($list);
	$sp = "  ";
	while(list($k,$v) = each($list)) {
		$hv = sendhelp($v,$help[$k]);
		print $sp."<TH BGCOLOR=\"$head_color\">$hv</TH>\n";
		$sp .= "  ";
	}
	print "</TR>\n";

// list batches owned by user
reset($_SESSION['user']->bids);
while (list($k,$v) = each($_SESSION['user']->bids)) {
	$fn = $db->batch_fieldnames($k);
print	row(cell($k,"ALIGN=\"RIGHT\" BGCOLOR=\"$head_color\"")
	."\n  ".cell(senddesc($v["batch"],$k,"batch"))
	."\n    ".cell(senddesc($fn["num"],$k,"num"))
	."\n      ".cell(senddesc($fn["title"],$k,"title"))
	."\n        ".cell(senddesc($fn["card"],$k,"card"))
	."\n          ".cell(related_bid($k))
	."\n            ".cell(count_bid($k),"ALIGN=\"RIGHT\""));
}

print <<<PAGE
	</TABLE><!--}-->
</TH></TR>
</TABLE><!--}-->
PAGE;
	if ($phpinfo) {phpinfo();}
print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
