<?php
// DESC: output the selected batches as a CSV file
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

if (! $view) {
	$view = new View();
}

if ($user->selected_count() == 0) {
	card_head("No Selected Batches");
print <<< PAGE

<h2>No Selected Batches</h2>
<p align="justified">Please select at least one batch from 
</p>
<p>
<a href="sel_batches.php">Click here</a> to make a selection
</p>

PAGE;
	card_foot();
	return;
}

// get cards from db
$cards = $db->cards_(&$user->bids);
$view->sort($cards);
//$db->dumper($cards);

$strName="csv_cards.txt";
header("Content-type: text/ascii name=\"$strName\"");
Header("Content-Disposition: inline; filename=\"$strName\"");

// csv output the cards according to view parameters
function csv_out($t) {
	$quoted = false;
// check for quotes
	if (preg_match('/"/' ,$t )) {
		$quoted = true;
		$t = preg_replace("/\"/","\"\"",$t);
	}
	if (preg_match("/\n/" ,$t )) {
		$quoted = true;
		$t = preg_replace("/\r/","",$t);
		$t = preg_replace("/\n/","\\n",$t);
	}
	if (preg_match("/,/" ,$t )) {
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
		if(! preg_match("/^$/",$user->bids[$v[bid]][num])) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][num]));
		}
		if(! preg_match("/^$/",$user->bids[$v[bid]][title])) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][title]));
		}
		if (($view->body == "full")
		&& (! preg_match("/^$/",$user->bids[$v[bid]][card]))) {
			array_push($stack,
				csv_out($user->bids[$v[bid]][card]));
		}
		print join(",",$stack)."\n";
	}
	$stack = array();
	if(! preg_match("/^$/",$user->bids[$v[bid]][num])) {
		array_push($stack, csv_out($v[num]));
	}
	if(! preg_match("/^$/",$user->bids[$v[bid]][title])) {
		array_push($stack, csv_out($v[title]));
	}
	if (($view->body == "full")
	&& (! preg_match("/^$/",$user->bids[$v[bid]][card]))) {
		array_push($stack, csv_out($v[card]));
	}
	print join(",",$stack)."\n";
}

showphpinfo();

?>
