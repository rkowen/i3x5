<?php
//
// DESC: one batch selector class
//

include_once "user.inc";
include_once "cards.inc";
include_once "javascript.inc";

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

	// This code snippet handles retrieving the one_batch_* selectors
	$t = "one_batch_".$this->id;
	if (array_key_exists($t,$_GET) && $_GET[$t]) {
		$_POST[$t] = $_GET[$t];
	}
	if (array_key_exists($t,$_POST) && $_POST[$t]) {
		$this->bid = $_POST[$t];
	}
	// maybe something fancy (id="[...]" array)
	$f = preg_replace("/[\[\]]/","",$this->id);
	if (($f != $this->id) and is_array($_POST["one_batch_"])) {
		$this->bid = $_POST["one_batch_"][$f];
	}

	return $this->bid;
}

// return the javascript select array
// (if $selected then only those identified in $user as selected)
function js_one_array($arrayName = "selectOneArray", $selectd=false) {
	global $user;

	$s = "var $arrayName = new Array(\n";

	// list batches owned by user
	reset($user->bids);
	while (list($k,$v) = each ($user->bids)) {
		if ((isset($selected) && ! $selected)
		||   isset($v["selected"])) {
			$s .= "new Option('{$v["batch"]}','$k'),";
		}
	}
	// strip trailing ,
	$s = ereg_replace(",\$","",$s);
	$s .= ");\n";
	return $s;
}

// return a SELECT reference to the javascript menu function
// using the id as a SELECT identifier
function js_string_one_batch($arrayName = "selectOneArray") {
	return js_select("one_batch_{$this->id}",
		"this.form.one_batch_{$this->id}", $arrayName);
}

// return the select table
// (if $selected then only those identified in $user as selected)
function string_one_batch($selected = false) {
	global $user;

	$s = "<!--one_batch-->\n"
		."<SELECT NAME=\"one_batch_{$this->id}\" SIZE=1>\n";

	// list batches owned by user
	reset($user->bids);
	while (list($k,$v) = each ($user->bids)) {
		if ((! $selected) || $v["selected"]) {
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

	// This code snippet handles retrieving the one_batch_* selectors
	$t = "ops_batch_".$this->id;
	if (array_key_exists($t,$_POST) && $_POST[$t]) {
		$this->ops = $_POST[$t];
	}
	// maybe something fancy (id="[...]" array)
	$f = preg_replace("/[\[\]]/","",$this->id);
	if (($f != $this->id) and is_array($_POST["ops_batch_"])) {
		$this->ops = $_POST["ops_batch_"][$f];
	}

	return $this->ops;
}

// return a SELECT reference to the Relate/Copy/Move javascript menu function
// using the id as a SELECT identifier
function js_string_ops_batch($arrayName = "selectOneArray") {
	return js_select("ops_batch_{$this->id}",
		"this.form.ops_batch_{$this->id}", "selectRCM");
}

function string_ops_batch($delete = false) {
	return 
		"<!--ops_batch-->\n"
		."<SELECT NAME=\"ops_batch_{$this->id}\" SIZE=1>\n"
		.($delete?"":"<OPTION SELECTED VALUE=\"NONE\"></OPTION>\n")
		."<OPTION VALUE=\"RELATE\">Relate To</OPTION>\n"
		."<OPTION VALUE=\"COPY\">Copy To</OPTION>\n"
		."<OPTION VALUE=\"MOVE\">Move To</OPTION>\n"
	.($delete?"<OPTION VALUE=\"DELETE\">Delete Checked</OPTION>\n":"")
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
