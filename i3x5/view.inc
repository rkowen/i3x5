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

// constructor
function View (	$edit	= "edit",	// or "list"
		$body	= "full",	// or "header"
		$dates	= "show",	// or "hide"
		$key	= "num",	// or "title","cdate","mdate"
		$order	= "des",	// or "asc"
		$group	= "batch") {	// or "none"

	global $user;
	global $level_read;

	// check the access level
	if ($user->level <= $level_read) {
		$this->edit	= "list";
	} else {
		$this->edit	= $edit;
	}
	$this->body	= $body;
	$this->dates	= $dates;
	$this->key	= $key;
	$this->order	= $order;
	$this->group	= $group;
}

// catch_gets - capture those view values we want to override via the URL
function catch_gets() {
	global $user;
	global $level_read;

	// Allow the _GET to override the following:
	if (array_key_exists("view_edit",$_GET)
	&& ($_GET["view_edit"] == "edit"
	|| $_GET["view_edit"] == "list")) {
		$this->edit = $_GET["view_edit"];
	}
	if ($user->level <= $level_read) {
		$this->edit	= "list";
	}
}

// retrieve the posted values and update attributes as needed
function get_buttons() {

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
		$t = "view_".$v;
		if ($_POST[$t]) {
			$this->$v = $_POST[$t];
		}
	}
}

// show the radio button table
function show_buttons() {
	global $user;
	global $level_read;
	global $noselect_color;

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
	print table(
	row(	head("Card View Attributes", "COLSPAN=5"))
	.row(	cell(sendhelp("Cards:","view cards"))
		.cell(
		($user->level <= $level_read
		? "<FONT COLOR=\"$noselect_color\">Edit</FONT>\n"
		: input("radio","view_edit","edit",$sel_edit)." Edit"),
		"COLSPAN=2")
		.cell(	input("radio","view_edit","list",$sel_list)." List",
		"COLSPAN=2"))
	.row(	cell(sendhelp("Show:","view show"))
		.cell(	input("radio","view_body","full",$sel_full)
			." Full", "COLSPAN=2")
		.cell(	input("radio","view_body","header",$sel_header)
			." Header Only", "COLSPAN=2"))
	.row(	cell(sendhelp("Dates:","view dates"))
		.cell(	input("radio","view_dates","show",$sel_dates_show)
			." Show dates", "COLSPAN=2")
		.cell(	input("radio","view_dates","hide",$sel_dates_hide)
			." Hide dates", "COLSPAN=2"))
	.row(	cell(sendhelp("Key:","view key"))
		.cell(	input("radio","view_key","num",$sel_num)." Number")
		.cell(	input("radio","view_key","title",$sel_title)." Title")
		.cell(	input("radio","view_key","mdate",$sel_mdate)." Mdate")
		.cell(	input("radio","view_key","cdate",$sel_cdate)." Cdate"))
	.row(	cell(sendhelp("Order:","view order"))
		.cell(	input("radio","view_order","des",$sel_des)
			." Descending", "COLSPAN=2")
		.cell(	input("radio","view_order","asc",$sel_asc)
			." Ascending", "COLSPAN=2"))
	.row(	cell(sendhelp("Group By:","view group"))
		.cell(	input("radio","view_group","batch",$sel_batch)
			." Batch",
		"COLSPAN=2")
		.cell(	input("radio","view_group","none",$sel_none)
			." None",
		"COLSPAN=2"))
,"WIDTH=\"100%\" BORDER=1");
}


// takes the $cards hash and sort it according to the view parameters
function sort(&$cards) {
	uasort($cards, "card_cmp");
} 

// display the cards according to view parameters
function cards(&$cards, $check_all = false) {
	global $user;
	global $form_color;
	global $relate_color;
	global $related_color;
	global $head_color;
	global $level_write;
	global $db;

	$entrytableopt = "BORDER=1 WIDTH=100% CELLPADDING=4";

	reset($cards);
	print "<TABLE BORDER=1 WIDTH=100% BGCOLOR=\"$form_color\">\n";

	if ($this->edit == "edit") {
		$lines = 3;
		print row(head(
	sendhelp("Help","update all cards")
	.input("submit","upd_all","Update All Cards")." ("
	.sendhelp(inform("only non-relation cards") ,"update relation").")"));

	$bheader = "";
//{ insert card
	reset($user->bids);
	// get first selected batch
	while(list($k,$v) = each($user->bids)) {
		if ($v["selected"]) { break; }
	}
	$fn = $db->batch_fieldnames($k);

	$ibatch = new OneBatch("i",$k);
	print "<TR><TD>\n"
	.table(
		row(	cell(senddesc($fn["num"],$k,"num")
				,"BGCOLOR=\"$head_color\" WIDTH=10% ")
			.cell(
			  input("text","i_num","","SIZE=16 MAXLENGTH=20")."\n"
				,"WIDTH=40% COLSPAN=2")
			.head($ibatch->string_one_batch(true)
				,"WIDTH=25%")
			.cell(input("submit","ins__","Insert")."\n"
				,"WIDTH=25% ALIGN=\"CENTER\""))
		.row(	cell(senddesc($fn["title"],$k,"title")
				,"BGCOLOR=\"$head_color\" WIDTH=10%")
			.cell(input("text","i_title","","SIZE=80 MAXLENGTH=80")
				,"WIDTH=90% COLSPAN=3")
			.cell(input("checkbox","i_formatted","true")
				.sendhelp("Formatted","formatted")."\n"
				,"WIDTH=10% COLSPAN=1"))
		.row(	cell(senddesc($fn["card"],$k,"card")
			,"WIDTH=10% BGCOLOR=\"$head_color\" VALIGN=\"top\"")
			.cell(textarea("i_card","","COLS=80 ROWS=$lines")
				,"WIDTH=90% COLSPAN=4"))
	,$entrytableopt)
	."</TD></TR>\n";
//}
	}
	while (list($k,$v) = each($cards)) {
		$c_nrid = $v["nrid"];
		$c_rid = $v["rid"];
		if ($v["rid"]) {
			$xcolor = $relate_color;
			$xid = "$k (".inform($v["rid"]).")";
			$rcolor = $form_color;
			$delmsg = "Delete";
		} else {
			$xcolor = $form_color;
			$xid = $k;
			if ($v["nrid"]) {
				$rcolor = $related_color;
				$delmsg = "Delete All";
			} else {
				$rcolor = $form_color;
				$delmsg = "Delete";
			}
		}
// batch header
		if ($this->group == "batch"
		&&  $bheader != $user->bids[$v["bid"]]["batch"]) {
			$bheader = $user->bids[$v["bid"]]["batch"];
			print "<TR><TD>\n"
			.table( row( head( senddesc($bheader,$v["bid"],"batch"),
			"ALIGN=\"LEFT\" COLSPAN=5 BGCOLOR=\"$head_color\""))
				,$entrytableopt)
			."</TD></TR>\n";
		}
if ($this->edit == "list") {
	$card = $this->br($v["card"]);
	if (ereg("^$", $user->bids[$v["bid"]]["num"])) {
		$tcolspan = 3;
		$tper = "80%";
		$tid = cell($xid,"COLSPAN=1 WIDTH=10% ALIGN=\"RIGHT\"");
	} else {
		$tcolspan = 4;
		$tper = "90%";
		$tid = "";
	}
	print "<TR><TD>\n"
	.table(
		(! ereg("^$",$user->bids[$v["bid"]]["num"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["num"]
				,$v["bid"],"num")
				,"BGCOLOR=\"$head_color\" WIDTH=10%")
			.cell($v["num"],"WIDTH=80%")
			.cell($xid,"COLSPAN=3 WIDTH=10% ALIGN=\"RIGHT\"") )
			: "" )
	.(! ereg("^$",$user->bids[$v["bid"]]["title"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
				,$v["bid"],"title")
				,"BGCOLOR=\"$head_color\" WIDTH=10%")
			.cell($v["title"],"WIDTH=$tper COLSPAN=$tcolspan").$tid)
		: "" )
	.((($this->body == "full")
		&& (! ereg("^$",$user->bids[$v["bid"]]["card"])))
		? row(	cell(senddesc($user->bids[$v["bid"]]["card"]
				,$v["bid"],"card")
				,"BGCOLOR=\"$head_color\" WIDTH=10%")
			.cell(($v["formatted"]=="t"
				? pre($v["card"],"t") : $this->br($v["card"]))
				,"WIDTH=90% COLSPAN=4")) : "")
	,$entrytableopt)
	."</TD></TR>\n";
} else {
	if ($user->level >= $level_write) {
	$card = $this->br($v["card"]);
	$cbatch[$k] = new OneBatch("[$k]",$v["bid"]);
	$lines = max(count(preg_split("/\n/", $v["card"])),
			strlen($v["card"])/75) + 1;
	print "<TR><TD>\n"
	.table(
		row(	(! ereg("^$",$user->bids[$v["bid"]]["num"])
			?cell(senddesc($user->bids[$v["bid"]]["num"],$v["bid"],
				"num"),"BGCOLOR=\"$head_color\" WIDTH=10%")
			.cell(input("text","c_num[$k]",$v["num"],
				"SIZE=16 MAXLENGTH")
				,"BGCOLOR=\"$xcolor\" WIDTH=30%")
			: cell("&nbsp;","COLSPAN=2 WIDTH=40%") )
			.cell(input("checkbox","c_check[$k]","true",
				($check_all?"CHECKED":""))
				.$xid,"WIDTH=10% ALIGN=\"CENTER\"")
			.head(	 $cbatch[$k]->string_ops_batch()
				.$cbatch[$k]->string_one_batch()
				,"WIDTH=25%")
			.cell(	 input("hidden","c_rid[$k]",$c_rid)."\n"
				.input("hidden","c_nrid[$k]",$c_nrid)."\n"
				.input("submit","upd_[$k]","Update")."&nbsp;\n"
				.input("submit","del_[$k]",$delmsg)."\n"
				,"WIDTH=25% ALIGN=\"CENTER\" BGCOLOR=$rcolor"))
	.(! ereg("^$",$user->bids[$v["bid"]]["title"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
				,$v["bid"],"title")
				,"WIDTH=10% BGCOLOR=\"$head_color\"")
			.cell(	input("text","c_title[$k]",$v["title"],
					"SIZE=80 MAXLENGTH=80")
				,"WIDTH=90% BGCOLOR=\"$xcolor\" COLSPAN=3")
			.cell("\n".input("checkbox","c_formatted[$k]","true",
				($v["formatted"]=="t" ? "CHECKED" : ""))
				.sendhelp("Formatted","formatted")."\n"
				,"WIDTH=10% COLSPAN=1"))
		: "")
	.((($this->body == "full")
		&& ! ereg("^$",$user->bids[$v["bid"]]["card"]))
		? row(	cell(senddesc($user->bids[$v["bid"]]["card"]
				,$v["bid"],"card")
				,"WIDTH=10% BGCOLOR=\"$head_color\" "
				."VALIGN=\"TOP\"")
			.cell(textarea("c_card[$k]",$v["card"],
				"COLS=80 ROWS=$lines")
				,"WIDTH=90% COLSPAN=4")) : "")
	.($this->dates != "hide" ?
		row(	cell(	table(	row(
				head("created: ".$v["cdate"])
				.head("modified: ".$v["mdate"])),
			"WIDTH=\"100%\" BORDER=0"),
		"ALIGN=\"CENTER\" COLSPAN=5 BGCOLOR=\"$head_color\""))
		: "")
	,$entrytableopt)
	."</TD></TR>\n";
	} else {	// append only
		$card = $this->br($v["card"]);
	print "<TR><TD>\n"
	.table(	row(	(! ereg("^$", $user->bids[$v["bid"]]["num"])
			?cell(senddesc($user->bids[$v["bid"]]["num"] ,$v["bid"],
				"num"),"WIDTH=10% BGCOLOR=\"$head_color\"")
			 .cell($v["num"],"WIDTH=40%")
			: cell(" ","WIDTH=50% COLSPAN=2") )
			.cell(	 input("hidden","c_rid[$k]",$c_rid)
				.input("hidden","c_nrid[$k]",$c_nrid)
				.input("submit","upd_[$k]","Append")
				,"COLSPAN=2 WIDTH=40% ALIGN=\"CENTER\"")
			.cell($xid,"WIDTH=10% ALIGN=\"RIGHT\"") )
	.(! ereg("^$",$user->bids[$v["bid"]]["title"])
		? row(	cell(senddesc($user->bids[$v["bid"]]["title"]
				,$v["bid"],"title")
				,"WIDTH=10% BGCOLOR=\"$head_color\"")
			.cell($v["title"],"WIDTH=90% COLSPAN=4"))
		: "" )
	.((($this->body == "full")
		&& ! ereg("^$",$user->bids[$v["bid"]]["card"]))
		? row(	cell(senddesc($user->bids[$v["bid"]]["card"]
				,$v["bid"],"card")
				,"WIDTH=10% BGCOLOR=\"$head_color\" "
				."VALIGN=\"TOP\"")
			.cell($card."<BR>"
			.textarea("c_card[$k]",$v["card"],"COLS=80 ROWS=2")
				,"WIDTH=90% BGCOLOR=\"$xcolor\" COLSPAN=4"))
		: "")
	,$entrytableopt)
	."</TD></TR>\n";
	}
}
	}
	if ($this->edit == "edit") {
		print row(head(
	sendhelp("Help","update all cards")
	.input("submit","upd_all","Update All Cards")." ("
	.sendhelp(inform("only non-relation cards") ,"update relation").")"));
	}
	print "</TABLE>\n";
}

// return true if editable situation else false
function is_edit() {
	return ($this->edit == "edit" ? true : false);
}

// put in <BR>s wherever there is a newline
function br (&$card) {
	return preg_replace("/\n/","<BR>\n",$card);
}

function dump() {

	$a = get_object_vars($this);
	reset($a);
	while (list($k, $v) = each($a)) {
		print "\$view->$k	= $v<BR>\n";
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
