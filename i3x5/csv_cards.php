<?php
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

if (! $view) {
	$view = new View();
}

if ($user->selected_count() == 0) {
print <<< PAGE
<HTML>
<BODY $result_bg>

<H2>No Selected Batches</H2>
<P ALIGN="justified">Please select at least one batch from 
<P>
<A HREF="sel_batches.php">Click here</A> to make a selection

</BODY>
</HTML>
PAGE;
	return;
}

// get cards from db
$cards = $db->cards(&$user->bids);
$view->sort($cards);
//$db->dumper($cards);

$strName="csv_cards.txt";
header("Content-type: text/ascii name=\"$strName\"");
Header("Content-Disposition: inline; filename=\"$strName\"");

// csv output the cards according to view parameters
function csv_out($t) {
	$quoted = false;
// check for quotes
	if (ereg("\"" ,$t )) {
		$quoted = true;
		$t = preg_replace("/\"/","\"\"",$t);
	}
	if (ereg("\n" ,$t )) {
		$quoted = true;
		$t = preg_replace("/\r/","",$t);
		$t = preg_replace("/\n/","\\n",$t);
	}
	if (ereg("," ,$t )) {
		$quoted = true;
	}
	if ($quoted) {
		$t = "\"".$t."\"";
	}
	return $t;
}

reset($cards);
$firsttime = 1;

while (list($k,$v) = each($cards)) {

	if ($firsttime) {
		// use first card batch for field names
		$stack = array();
		$firsttime = 0;
		if(! ereg("^$",$user->bids[$v[bid]][num])) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][num]));
		}
		if(! ereg("^$",$user->bids[$v[bid]][title])) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][title]));
		}
		if (($view->body == "full")
		&& (! ereg("^$",$user->bids[$v[bid]][card]))) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][card]));
		}
		print join(",",$stack)."\n";
	}
	$stack = array();
	if(! ereg("^$",$user->bids[$v[bid]][num])) {
		array_push($stack, csv_out($v[num]));
	}
	if(! ereg("^$",$user->bids[$v[bid]][title])) {
		array_push($stack, csv_out($v[title]));
	}
	if (($view->body == "full")
	&& (! ereg("^$",$user->bids[$v[bid]][card]))) {
		array_push($stack, csv_out($v[card]));
	}
	print join(",",$stack)."\n";
}

if ($phpinfo) {phpinfo();}

?>
