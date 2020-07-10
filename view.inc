<?php
//
// DESC: View class handles the batch view properties
// DESC: and viewing the card batches
//

include_once "user.inc";
include_once "cards.inc";
include_once "one_batch.inc";

class View {
	var $edit;
	var $body;
	var $dates;
	var $key;
	var $order;
	var $group;
	var $cards;

// constructor
function __construct (
		$edit	= "edit",	// or "list"
		$body	= "full",	// or "header"
		$dates	= "hide",	// or "show"
		$key	= "mdate",	// or "num","title","cdate"
		$order	= "des",	// or "asc"
		$group	= "batch") {	// or "none"

	global $user;
	global $level_read;

	// check the access level
	if (isset($user->level)
	and ($user->level <= $level_read)) {
		$this->edit	= "list";
	} else {
		$this->edit	= $edit;
	}
	$this->body	= $body;
	$this->dates	= $dates;
	$this->key	= $key;
	$this->order	= $order;
	$this->group	= $group;
	$this->cards	= NULL;
}

// catch_gets - capture those view values we want to override via the URL
function catch_gets() {
	global $user;
	global $level_read;

	// Allow the _GET to override the following:
	if (isset($_GET["view_edit"])
	&& ($_GET["view_edit"] == "edit"
	|| $_GET["view_edit"] == "list")) {
		$this->edit = $_GET["view_edit"];
	}
	if ($user->level <= $level_read) {
		$this->edit	= "list";
	}
	if (isset($_GET["bid"])) {
		$user->bid	= $_GET["bid"];
	}
}

// read/set lastcardsql
function lastcardsql($sql = "") {
	if ($sql == "") {
		if (isset($this->lastcardsql)) {
			return $this->lastcardsql;
		}
	} else {
		$this->lastcardsql = $sql;
	}
}

// generate an XML representation of the view settings
function xml_settings($indent = "") {
	$buffer=$indent."<settings>\n";
	global $user;
	global $level_write;
	global $level_read;

	$sel_edit	= ($this->edit	== "edit" ? ' selected="1"' : "");
	$sel_list	= ($this->edit	== "list" ? ' selected="1"' : "");
	$sel_header	= ($this->body	== "header" ? ' selected="1"' : "");
	$sel_full	= ($this->body	== "full" ? ' selected="1"' : "");
	$sel_dates_show	= ($this->dates	== "show" ? ' selected="1"' : "");
	$sel_dates_hide	= ($this->dates	== "hide" ? ' selected="1"' : "");
	$sel_num	= ($this->key	== "num" ? ' selected="1"' : "");
	$sel_title	= ($this->key	== "title" ? ' selected="1"' : "");
	$sel_cdate	= ($this->key	== "cdate" ? ' selected="1"' : "");
	$sel_mdate	= ($this->key	== "mdate" ? ' selected="1"' : "");
	$sel_asc	= ($this->order	== "asc" ? ' selected="1"' : "");
	$sel_des	= ($this->order	== "des" ? ' selected="1"' : "");
	$sel_onone	= ($this->order	== "none" ? ' selected="1"' : "");
	$sel_batch	= ($this->group	== "batch" ? ' selected="1"' : "");
	$sel_none	= ($this->group	== "none" ? ' selected="1"' : "");

	$buffer.=$indent."  <user level=\"$user->level\"/>\n";
	$buffer.=$indent."  <write level=\"$level_write\"/>\n";
	$buffer.=$indent."  <read level=\"$level_read\"/>\n";
	$buffer.=$indent."  <action type=\"edit\"$sel_edit/>\n";
	$buffer.=$indent."  <action type=\"list\"$sel_list/>\n";
	$buffer.=$indent."  <view type=\"header\"$sel_header/>\n";
	$buffer.=$indent."  <view type=\"full\"$sel_full/>\n";
	$buffer.=$indent."  <dates type=\"show\"$sel_dates_show/>\n";
	$buffer.=$indent."  <dates type=\"hide\"$sel_dates_hide/>\n";
	$buffer.=$indent."  <key type=\"num\"$sel_num/>\n";
	$buffer.=$indent."  <key type=\"title\"$sel_title/>\n";
	$buffer.=$indent."  <key type=\"cdate\"$sel_cdate/>\n";
	$buffer.=$indent."  <key type=\"mdate\"$sel_mdate/>\n";
	$buffer.=$indent."  <order type=\"asc\"$sel_asc/>\n";
	$buffer.=$indent."  <order type=\"desc\"$sel_des/>\n";
	$buffer.=$indent."  <order type=\"none\"$sel_onone/>\n";
	$buffer.=$indent."  <group type=\"batch\"$sel_batch/>\n";
	$buffer.=$indent."  <group type=\"none\"$sel_none/>\n";
	$buffer.=$indent."</settings>\n";

	return $buffer;
}

// retrieve the posted values and update attributes as needed
// if need more than one then specify a unique $n
function get_buttons($n = "") {

	$view_stuff = array (	"edit",
				"body",
				"dates",
				"key",
				"order",
				"group");

	// This code snippet handles retrieving the view_* buttons
	// if the variables have been posted then read them in to the vars
	reset($view_stuff);
	while (list($k,$v) = each($view_stuff)) {
		$t = "view_".$v.$n;
		if (isset($_POST[$t])) {
			$this->$v = $_POST[$t];
		}
	}
}

// show the radio button table
// if need more than one then specify a unique $n
function string_buttons($n = "") {
	global $user;
	global $level_read;

	$sel_edit	= ($this->edit	== "edit" ? "CHECKED" : "");
	$sel_list	= ($this->edit	== "list" ? "CHECKED" : "");
	$sel_header	= ($this->body	== "header" ? "CHECKED" : "");
	$sel_full	= ($this->body	== "full" ? "CHECKED" : "");
	$sel_dates_show	= ($this->dates	== "show" ? "CHECKED" : "");
	$sel_dates_hide	= ($this->dates	== "hide" ? "CHECKED" : "");
	$sel_num	= ($this->key	== "num" ? "CHECKED" : "");
	$sel_title	= ($this->key	== "title" ? "CHECKED" : "");
	$sel_cdate	= ($this->key	== "cdate" ? "CHECKED" : "");
	$sel_mdate	= ($this->key	== "mdate" ? "CHECKED" : "");
	$sel_asc	= ($this->order	== "asc" ? "CHECKED" : "");
	$sel_des	= ($this->order	== "des" ? "CHECKED" : "");
	$sel_batch	= ($this->group	== "batch" ? "CHECKED" : "");
	$sel_none	= ($this->group	== "none" ? "CHECKED" : "");

	// card edit buttons
return "<!--{view-->".table(
row(	cell("Card View Attributes",
		"COLSPAN=5 class=\"h_form\" id=\"center\""))
.row(	cell(sendhelp("Cards:","view cards"),"class=\"h_form\" id=\"left\"")
	.cell(
	($user->level <= $level_read
		? noselect("Edit")."\n"
		: input("radio","view_edit$n","edit",$sel_edit)." Edit"),
	"colspan=2 id=\"left\"")
	.cell(	input("radio","view_edit$n","list",$sel_list)." List",
	"colspan=2 id=\"left\""))
.row(	cell(sendhelp("Show:","view show"),"class=\"h_form\" id=\"left\"")
	.cell(	input("radio","view_body$n","full",$sel_full)
		." Full", "colspan=2 id=\"left\"")
	.cell(	input("radio","view_body$n","header",$sel_header)
		." Header Only", "colspan=2 id=\"left\""))
.row(	cell(sendhelp("Dates:","view dates"),"class=\"h_form\" id=\"left\"")
	.cell(	input("radio","view_dates$n","hide",$sel_dates_hide)
		." Hide dates", "colspan=2 id=\"left\"")
	.cell(	input("radio","view_dates$n","show",$sel_dates_show)
		." Show dates", "colspan=2 id=\"left\""))
.row(	cell(sendhelp("Key:","view key"),"class=\"h_form\" id=\"left\"")
	.cell(	input("radio","view_key$n","mdate",$sel_mdate)." Mdate",
		"id=\"left\"")
	.cell(	input("radio","view_key$n","cdate",$sel_cdate)." Cdate",
		"id=\"left\"")
	.cell(	input("radio","view_key$n","num",$sel_num)." Number",
		"id=\"left\"")
	.cell(	input("radio","view_key$n","title",$sel_title)." Title",
		"id=\"left\""))
.row(	cell(sendhelp("Order:","view order"),"class=\"h_form\" id=\"left\"")
	.cell(	input("radio","view_order$n","des",$sel_des)
		." Descending", "colspan=2 id=\"left\"")
	.cell(	input("radio","view_order$n","asc",$sel_asc)
		." Ascending", "colspan=2 id=\"left\""))
.row(	cell(sendhelp("Group By:","view group"),"class=\"h_form\" id=\"left\"")
	.cell(	input("radio","view_group$n","batch",$sel_batch)
		." Batch",
	"colspan=2 id=\"left\"")
	.cell(	input("radio","view_group$n","none",$sel_none)
		." None",
	"colspan=2 id=\"left\""))
,"class=\"form\" width=\"100%\" border=1")."<!--}-->\n";
}

// show the radio button table
function show_buttons($n = "") {
	print $this->string_buttons($n);
}

// takes the $cards hash and sort it according to the view parameters
function sort(&$cards) {
	uasort($cards, "card_cmp");
}

// display the cards according to view parameters
function string_cards(&$cards, $check_all = false) {
	global $user;
	global $level_write;
	global $db;

//	$entrytableopt = "border=1 width=100% cellpadding=4";
	$entrytableopt = "class=\"cardview\"";

	reset($cards);
	$text = "<table width=100% class=\"batch\">\n";

if ($this->edit == "edit") {
		$lines = 3;
		$text .= row(head(
	sendhelp("Help","update all cards")
	.input("submit","upd_all","Update All Cards")." ("
	.sendhelp(inform("only non-relation cards") ,"update relation").")"));
}

	$bheader = "";

if ($this->edit == "edit") {
//{ insert card
	reset($user->bids);
	// get first selected batch
	while(list($k,$v) = each($user->bids)) {
		if ($v["selected"]) { break; }
	}
	$fn = $db->batch_fieldnames($k);

	$ibatch = new OneBatch("i",$k);
	$text .= row(cell(table(
		row(	cell(senddesc($fn["num"],$k,"num")
				,"class=\"h_number\" width=10% ")
			.cell(
			  input("text","i_num","","size=16 maxlength=20")."\n"
				,"class=\"b_number\" width=35% colspan=2")
			.head($ibatch->string_one_batch(true)
				,"width=35%")
			.cell(input("submit","ins__","Insert")."\n"
				,"width=20% id=\"center\""))
		.row(	cell(senddesc($fn["title"],$k,"title")
				,"class=\"h_title\" width=10% colspan=1")
			.cell(input("text","i_title","","size=80 maxlength=80")
				,"class=\"b_title\" width=70% colspan=3")
			.cell(input("checkbox","i_formatted","true")
				.sendhelp("Formatted","formatted")."\n"
				,"class=\"b_title\" width=20% colspan=1"))
		.row(	cell(senddesc($fn["card"],$k,"card")
			,"class=\"h_card\" width=10% valign=\"top\"")
			.cell(textarea("i_card","","cols=80 rows=$lines")
				,"class=\"b_card\" width=90% colspan=4"))
	,"class=\"cardview\"")))."\n";
//}
}
	while (list($k,$v) = each($cards)) {
		$c_nrid = $v["nrid"];
		$c_rid = $v["rid"];
		if ($v["rid"]) {
			$xid = "$k (".$v["rid"].")";
			$tcolor = "none";
			$rcolor = "none";
			$delmsg = "Delete";
			$entrytableopt = "class=\"cardview_relate\"";
		} else {
			$xid = $k;
			$tcolor = "b_number";
			if ($v["nrid"]) {
				$rcolor = "related_warn";
				$delmsg = "Delete All";
			} else {
				$rcolor = "b_number";
				$delmsg = "Delete";
			}
			$entrytableopt = "class=\"cardview\"";
		}
// batch header
		if (($this->group == "batch")
		&& ((! isset($bheader))
		|| $bheader != $user->bids[$v["bid"]]["batch"])) {
			$bheader = $user->bids[$v["bid"]]["batch"];
			$text .= row(cell(
			table( row( cell( senddesc($bheader,$v["bid"],"batch"),
			"class=\"h_batch\" id=\"left\" colspan=5"))
				,$entrytableopt)))."\n";
		}
if ($this->edit == "list") {
	$card = $this->br($v["card"]);
	if (preg_match("/^$/", $user->bids[$v["bid"]]["num"])) {
		$tcolspan = 3;
		$tper = "80%";
		$tid = cell($xid." ".toghide($k, $this->body)
,"class=\"b_cid\" colspan=1 width=10%");
	} else {
		$tcolspan = 4;
		$tper = "90%";
		$tid = "";
	}
	$text .= row(cell(table(
		(! preg_match("/^$/",$user->bids[$v["bid"]]["num"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["num"]
				,$v["bid"],"num")
				,"class=\"h_number\" width=10%")
			.cell($v["num"],"class=\"$tcolor\" width=\"80%\"")
			.cell($xid." ".toghide($k, $this->body)
,"class=\"b_cid\" colspan=3 width=10%") )
			: "" )
	.(! preg_match("/^$/",$user->bids[$v["bid"]]["title"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
				,$v["bid"],"title")
				,"class=\"h_title\" width=10%")
			.cell($v["title"],
		"class=\"b_title\" width=$tper colspan=$tcolspan").$tid)
		: "" )
	.row(	cell(senddesc($user->bids[$v["bid"]]["card"]
				,$v["bid"],"card")
				,"class=\"h_card\" width=10%")
			.cell(($v["formatted"]=="t"
				? pre($v["card"],"t") : $this->br($v["card"]))
				,"class=\"b_card\" width=90% colspan=4"),
	"id=\"card_$k\" class=\"togcard "
	.((($this->body == "full")
	&& ! preg_match("/^$/",$user->bids[$v["bid"]]["card"]))
	? "shown\"" : "hidden\""))
	.($this->dates != "hide" ?
		row(	cell(	table(	row(
			cell("created: ".$v["cdate"],"class=\"h_dates\"")
			.cell("modified: ".$v["mdate"],"class=\"h_dates\"")),
			"class=\"dates\" width=\"100%\""),
		"class=\"dates\" colspan=5"))
	: "")
	,$entrytableopt)))."\n";
} else {
	if ($user->level >= $level_write) {
	$card = $this->br($v["card"]);
	$cbatch[$k] = new OneBatch("[$k]",$v["bid"]);
	$lines = max(count(preg_split("/\n/", $v["card"])),
			strlen($v["card"])/75) + 1;
$text .= row(cell(table(
	row(	(! preg_match("/^$/",$user->bids[$v["bid"]]["num"])
		?cell(senddesc($user->bids[$v["bid"]]["num"],$v["bid"],
			"num"),"class=\"h_number\" width=10%")
		.cell(input("text","c_num[$k]",$v["num"],
			"size=16 maxlength=20")
			,"class=\"$tcolor\" width=20%")
		: cell("&nbsp;","class=\"$tcolor\" colspan=2 width=30%") )
		.cell(input("checkbox","c_check[$k]","true",
			($check_all?"CHECKED":""))
			.$xid.toghide($k,$this->body),"width=15% id=\"center\"")
		.head(	 $cbatch[$k]->js_string_ops_batch()
			.$cbatch[$k]->js_string_one_batch()
			,"width=35%")
		.cell(	 input("hidden","c_rid[$k]",$c_rid)."\n"
			.input("hidden","c_nrid[$k]",$c_nrid)."\n"
			.input("submit","upd_[$k]","Update")."&nbsp;\n"
			.input("submit","del_[$k]",$delmsg,
			  "onClick=\"return confirm('Are you sure?');\"")."\n"
			,"class=\"$rcolor\" width=20% id=\"center\""))
.(! preg_match("/^$/",$user->bids[$v["bid"]]["title"])
	? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
			,$v["bid"],"title")
			,"class=\"h_title\" width=10% colspan=1")
		.cell(	input("text","c_title[$k]",$v["title"],
				"size=80 maxlength=80")
			,"class=\"b_title\" width=70% colspan=3")
		.cell("\n".input("checkbox","c_formatted[$k]","true",
			($v["formatted"]=="t" ? "CHECKED" : ""))
			.sendhelp("Formatted","formatted")."\n"
			,"class=\"b_title\" width=20% colspan=1"))
	: "")
	.row(	cell(senddesc($user->bids[$v["bid"]]["card"]
			,$v["bid"],"card")
			,"class=\"h_card\" width=10% valign=\"TOP\"")
		.cell(textarea("c_card[$k]",$v["card"],
			"cols=80 rows=$lines")
			,"class=\"b_card\" width=90% colspan=4"),
	"id=\"card_$k\" class=\"togcard "
	.((($this->body == "full")
	&& ! preg_match("/^$/",$user->bids[$v["bid"]]["card"]))
	? "shown\"" : "hidden\""))
.($this->dates != "hide" ?
	row(	cell(	table(	row(
			cell("created: ".$v["cdate"],"class=\"h_dates\"")
			.cell("modified: ".$v["mdate"],"class=\"h_dates\"")),
		"class=\"dates\" width=\"100%\""),
	"class=\"dates\" colspan=5"))
	: "")
,$entrytableopt)))."\n";

	} else {	// append only
		$card = $this->br($v["card"]);
$text .= row(cell(table(
	row(	(! preg_match("/^$/", $user->bids[$v["bid"]]["num"])
		?cell(senddesc($user->bids[$v["bid"]]["num"],$v["bid"],
			"num"),"class=\"h_number\" width=10%")
		 .cell($v["num"],"class=\"$tcolor\" width=40%")
		: cell("&nbsp;","class=\"$tcolor\" width=50% colspan=2") )
		.cell(	 input("hidden","c_rid[$k]",$c_rid)
			.input("hidden","c_nrid[$k]",$c_nrid)
			.input("submit","upd_[$k]","Append")
			,"class=\"$tcolor\" colspan=2 width=40% id=\"center\"")
		.cell($xid,"class=\"b_cid\" width=10%") )
.(! preg_match("/^$/",$user->bids[$v["bid"]]["title"])
	? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
			,$v["bid"],"title")
			,"class=\"h_title\" width=10% colspan=1")
		.cell($v["title"],"class=\"b_title\" width=60% colspan=3")
		.cell("\n".input("checkbox","c_formatted[$k]","true",
			($v["formatted"]=="t" ? "CHECKED" : ""))
			.sendhelp("Formatted","formatted")."\n"
			,"class=\"b_title\" width=20% colspan=1"))
	: "" )
.((($this->body == "full")
	&& ! preg_match("/^$/",$user->bids[$v["bid"]]["card"]))
	? row(	cell(senddesc($user->bids[$v["bid"]]["card"]
			,$v["bid"],"card")
			,"class=\"h_card\" width=10% valign=\"top\"")
		.cell(($v["formatted"]=="t"
			? pre($v["card"],"t"):$this->br($v["card"]))
		.textarea("c_card[$k]","","cols=80 rows=2")
			,"class=\"b_card\" width=90% colspan=4"))
	: "")
.($this->dates != "hide" ?
	row(	cell(	table(	row(
			head("created: ".$v["cdate"])
			.head("modified: ".$v["mdate"])),
		"class=\"dates\" width=\"100%\""),
	"class=\"dates\" colspan=5"))
	: "")
,$entrytableopt)))."\n";
	}
}
	}
	if ($this->edit == "edit") {
		$text .= row(head(
	sendhelp("Help","update all cards")
	.input("submit","upd_all","Update All Cards")." ("
	.sendhelp(inform("only non-relation cards") ,"update relation").")"));
	}
	$text .= "</table>\n";

	return $text;
}

// display the cards according to view parameters
function cards(&$cards, $check_all = false) {
	print $this->string_cards($cards, $check_all);
}

// return true if editable situation else false
function is_edit() {
	return ($this->edit == "edit" ? true : false);
}

// put in <br/>s wherever there is a newline
function br (&$card) {
	return preg_replace("/\n/","<br/>",$card);
}

// reset the session values
function session () {
	if (isset($_SESSION["view"])) {
		$_SESSION["view"] = $view;
	}
}

// dump all the view object values
function dump() {

	$a = get_object_vars($this);
	reset($a);
	while (list($k, $v) = each($a)) {
		print "\$view->$k	= $v<br/>\n";
	}
}

}

// a comparison function for sorting a cards array, assumes a card array
// (id=>(id=>,bid=>,rid=>,num=>, title=>, card=>, cdate=>, mdate=> ),...)
// returns -1,0,1
// must be a stand-alone function (not a method)
function card_cmp(&$a, &$b) {
	global $view;
	global $user;

	if ($view->order == "asc") {
		$less = -1; $more =  1;
	} else {
		$less =  1; $more = -1;
	}
	if ($view->group == "batch") {	// sort by batch name
		if (	$user->bids[$a["bid"]]["batch"] >
			$user->bids[$b["bid"]]["batch"]) {
			return $less;
		}elseif($user->bids[$a["bid"]]["batch"] <
			$user->bids[$b["bid"]]["batch"]) {
			return $more;
		}
	}
	if ($view->key == "num") {		// sort by number
		if (	$a["num"] < $b["num"] ) {
			return $less;
		}elseif($a["num"] > $b["num"] ) {
			return $more;
		}
	} elseif ($view->key == "title") {	// sort by title
		if (	$a["title"] < $b["title"] ) {
			return $less;
		}elseif($a["title"] > $b["title"] ) {
			return $more;
		}
	} elseif ($view->key == "cdate") {	// sort by cdate
		if (	strtotime($a["cdate"]) < strtotime($b["cdate"]) ) {
			return $less;
		}elseif(strtotime($a["cdate"]) > strtotime($b["cdate"]) ) {
			return $more;
		}
	} elseif ($view->key == "mdate") {	// sort by mdate
		if (	strtotime($a["mdate"]) < strtotime($b["mdate"]) ) {
			return $less;
		}elseif(strtotime($a["mdate"]) > strtotime($b["mdate"]) ) {
			return $more;
		}
	}
	return 0;
}
?>
