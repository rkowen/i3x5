<?php
// DESC: List batch properties as a table
	include_once "cards.inc";
	include_once "user.inc";

	session_start();

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
	"misc"  => inform("Relation")
	);
	$help = array(
	"bid"	=> "batch id",
	"name"	=> "batch name",
	"number"=> "batch number",
	"title"	=> "batch title",
	"card"	=> "batch card",
	"misc"  => "batch relation"
	);

	function related_bid( $bid ) {
		global $db;
		global $user;

		$rbid = $db->sql("SELECT rid FROM i3x5_batch WHERE bid=$bid");

		if ($rbid) {
			return inform("Related to '".$user->bids[$rbid]["batch"]
				."' ($rbid)") ;
		}
		return;
	}

	$hhead = sendhelp("{$user->project} - List Batches", "list batches");
print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - List Batches</TITLE>
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
reset($user->bids);
while (list($k,$v) = each($user->bids)) {
	$fn = $db->batch_fieldnames($k);
print "<TR><TD ALIGN=RIGHT BGCOLOR=\"$head_color\">$k</TD>\n";
print "      <TD>".senddesc($v["batch"],$k,"batch")."</TD>\n";
print "        <TD>".senddesc($fn["num"],$k,"num")."</TD>\n";
print "          <TD>".senddesc($fn["title"],$k,"title")."</TD>\n";
print "            <TD>".senddesc($fn["card"],$k,"card")."</TD>\n";
print "              <TD>".related_bid($k)."</TD></TR>\n";
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
