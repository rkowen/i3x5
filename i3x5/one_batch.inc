<?php
//
// DESC: one batch selector class
//

include_once "user.inc";
include_once "cards.inc";

class OneBatch {
	var $id;	// should be a unique identifier
			// defaults to "_", which may not be unique
	var $bid;	// last selected bid
	var $ops;	// last operation requested

// constructor
function OneBatch (	$id	= "_",
			$bid = false) {

	$this->id	= $id;
	$this->bid	= $bid;
}

// retrieve the posted values and update attributes as needed
function get_one_batch() {

	global $HTTP_POST_VARS;
	global $HTTP_GET_VARS;

	// This code snippet handles retrieving the one_batch_* selectors
	$t = "one_batch_".$this->id;
	if ($HTTP_GET_VARS[$t]) {
		$HTTP_POST_VARS[$t] = $HTTP_GET_VARS[$t];
	}
	if ($HTTP_POST_VARS[$t]) {
		$this->bid = $HTTP_POST_VARS[$t];
	}
	// maybe something fancy (id="[...]" array)
	$f = preg_replace("/[\[\]]/","",$this->id);
	if (($f != $this->id) and is_array($HTTP_POST_VARS["one_batch_"])) {
		$this->bid = $HTTP_POST_VARS["one_batch_"][$f];
	}

	return $this->bid;
}

// show the select table
// (if $selected then only those identified in $user as selected)
function string_one_batch($selected = false) {
	global $user;

	$s = "<!--one_batch-->\n"
		."<SELECT NAME=\"one_batch_{$this->id}\" SIZE=1>\n";

	// list batches owned by user
	reset($user->bids);
	while (list($k,$v) = each ($user->bids)) {
		if ((! $selected) || $v[selected]) {
			$s .= "<OPTION"
			.($this->bid == $k ? " SELECTED " : " ")
			."VALUE=\"".$k."\">".$v["batch"]."</OPTION>\n";
		}
	}
	$s .= "</SELECT>\n";
	return $s;
}

function show_one_batch($selected = false) {
	print $this->string_one_batch($selected);
}

// retrieve the posted values and update attributes as needed
function get_ops_batch() {

	global $HTTP_POST_VARS;

	// This code snippet handles retrieving the one_batch_* selectors
	$t = "ops_batch_".$this->id;
	if ($HTTP_POST_VARS[$t]) {
		$this->ops = $HTTP_POST_VARS[$t];
	}
	// maybe something fancy (id="[...]" array)
	$f = preg_replace("/[\[\]]/","",$this->id);
	if (($f != $this->id) and is_array($HTTP_POST_VARS["ops_batch_"])) {
		$this->ops = $HTTP_POST_VARS["ops_batch_"][$f];
	}

	return $this->ops;
}

function string_ops_batch() {
	return 
		"<!--ops_batch-->\n"
		."<SELECT NAME=\"ops_batch_{$this->id}\" SIZE=1>\n"
		."<OPTION SELECTED VALUE=\"NONE\"></OPTION>\n"
		."<OPTION VALUE=\"RELATE\">Relate To</OPTION>\n"
		."<OPTION VALUE=\"COPY\">Copy To</OPTION>\n"
		."<OPTION VALUE=\"MOVE\">Move To</OPTION>\n"
		."</SELECT>\n";
}

function show_ops_batch() {
	print $this->string_ops_batch();
}

function dump() {

	$a = get_object_vars($this);
	reset($a);
	while (list($k, $v) = each($a)) {
		print "OneBatch->$k	= $v<BR>\n";
	}
}

}
?>
