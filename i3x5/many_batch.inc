<?php
//
// DESC: many batch selector class
//

include_once "user.inc";
include_once "cards.inc";

class ManyBatch {
	var $id;	// should be a unique identifier
			// defaults to "_", which may not be unique
	var $bids;	// selected bids

// constructor
function ManyBatch (	$id	= "_",
			$bids = array()) {

	$this->id	= $id;
	$this->bids	= $bids;
}

// retrieve the posted values and update attributes as needed
function get_many_batch() {

	global $user;

	$t = "many_batch_".$this->id;
	// selected status
	if (isset($_POST[$t."_selected"])) {
		$selected = $_POST[$t."_selected"];
	} else {
		$selected = false;
	}
	if (isset($_POST[$t]) && is_array($_POST[$t])) {
		$tbids = $_POST[$t];
		reset($tbids);
		// create some local variables
		$list = array();
		reset($user->bids);
		while (list($k,$v) = each($user->bids)) {
			if ((! $selected) || $v["selected"]) {
				$list[$k] = false;
			}
		}
		while (list($k, $v) = each($tbids)) {
			$sel_ = "sel_".$v;
			$$sel_ = true;
			if ($v != "_NOT_" && $v != "_ALL_") {
				$list[$v] = true;
			}
		}
		// see if all is checked then set them all
		if (isset($sel__ALL_)) {
			reset($user->bids);
			while (list($k,$v) = each($user->bids)) {
				if ((! $selected) || $v["selected"]) {
					$list[$k] = true;
				}
			}
		}
		// if not is checked then toggle them all
		if (isset($sel__NOT_)) {
			reset($user->bids);
			while (list($k,$v) = each($user->bids)) {
				if ((! $selected) || $v["selected"]) {
					$list[$k] = ! $list[$k];
				}
			}
		}
		// finally put them into bids
		unset($this->bids);
		$this->bids = array();
		reset($list);
		while (list($k,$v) = each($list)) {
			if($v) { $this->bids[] = $k; }
		}
	} else { // first time in set the selected from $user
		unset($this->bids);
		$this->bids = array();
		reset($user->bids);
		while (list($k,$v) = each($user->bids)) {
			if ($v["selected"]) {
				$this->bids[] = $k;
			}
		}
	}
	return $this->bids;
}

function set_user_selected() {
	global $user;
	// unset the current in $user
	reset($user->bids);
	while (list($k,$v) = each($user->bids)) {
		$user->bids[$k]["selected"] = 0;
	}
	// set the selected ones
	reset($this->bids);
	while (list($k,$v) = each($this->bids)) {
		$user->bids[$v]["selected"] = 1;
	}
}

// return the select table as a string
// (if $selected then only those identified in $user as selected)
// (note that this can be a rapidly diminishing set.)
function string_many_batch($selected = false) {
	global $user;

	// This code snippet handles the many_batch_* SELECTED
	$t = "many_batch_".$this->id;
	if (is_array($this->bids)) {
		reset($this->bids);
		while(list($k,$v) = each($this->bids)) {
			$sel_ = $t."_sel_".$v;
			$$sel_ = "SELECTED";
		}
	}
	$sel_A = "many_batch_".$this->id."_sel__ALL_";
	$sel_N = "many_batch_".$this->id."_sel__NOT_";
	$ssel_A = (isset($$sel_A)?$$sel_A:"");
	$ssel_N = (isset($$sel_N)?$$sel_N:"");
	$text = <<<PAGE

<!--{many_batch-->
<table width=100% border=0><tr><th valign="top">
<tr><th>
<select multiple name="many_batch_{$this->id}[]" size=5>
<option {$ssel_A} value="_ALL_">_ALL_</option>
<option {$ssel_N} value="_NOT_">_NOT_</option>

PAGE;
	// list batches owned by user
	reset($user->bids);
	while (list($k,$v) = each ($user->bids)) {
		$sel_ = "many_batch_".$this->id."_sel_".$k;
		if ((! $selected) || $v[selected]) {
			$text .= "<option ".(isset($$sel_)?$$sel_:"")
			." value=\"".$k."\">".$v["batch"]."</option>\n";
		}
	}
	$text .= <<<PAGE

</select>
</th><td valign="top">
PAGE;
	$text .= inform("Try left click or<BR>ctrl or shift-left click<BR>"
		."for multiple<BR>selections")."\n";
	$text .= "</td></tr></table>\n";

	// pass along status of $selected
	if ($selected) {
		$text .= "<input name=\"many_batch_{$this->id}_selected\""
			." type=\"hidden\" value=1>\n";
	}
	$text .= "<!--}-->\n";
	return $text;
}

// show the select table
// (if $selected then only those identified in $user as selected)
// (note that this can be a rapidly diminishing set.)
function show_many_batch($selected = false) {
	print $this->string_many_batch($selected);
}

function dump() {
	$a = get_object_vars($this);
	reset($a);
	while (list($k, $v) = each($a)) {
		print "ManyBatch->$k	= $v<BR>\n";
	}
	reset($this->bids);
	while (list($k, $v) = each($this->bids)) {
		print "ManyBatch->bids[$k]	= $v<BR>\n";
	}
	
}

}
?>
