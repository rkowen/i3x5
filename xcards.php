<?php
// DESC: view the individual cards as selected by the batch
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";
	include_once "javascript.inc";

if (! isset($view) || ! $user->selected_count()) {
	$_SESSION['view'] = new View();
	$view =& $_SESSION['view'];
	session_write_close();
	header("Location: sel_batches.php?errmsg="
		.urlencode("Should have clicked on \"Select Batches\" first!"));
	return;
}

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }
	$check_all = false;

//
// catch any GETS
//
	$view->catch_gets();
//
// process any changes
//

// process a global change only for real cards (not relations)
if (isset($_POST["upd_all"])
&& $_POST["upd_all"]=="Update All Cards"
&& is_array($_POST["c_rid"])) {
	foreach ($_POST["c_rid"] as $k => $v) {
		if (! $v) {
			if ($user->level >= $level_write) {
				$db->update_card($k,$v,
				array(
				"num"=>$_POST["c_num"][$k],
				"title"=>$_POST["c_title"][$k],
				"card"=>$_POST["c_card"][$k],
				"formatted"=>
					(isset($_POST["c_formatted"][$k])
					?$_POST["c_formatted"][$k]:false)
				));
			} else { 	// append only
				$db->append_card($k, $v,
					$_POST["c_card"][$k],
					(isset($_POST["c_formatted"][$k])
					?$_POST["c_formatted"][$k]:false));
			}
		}
	}
}
// process a selective update change
elseif (isset($_POST["upd_"])
&& is_array($_POST["upd_"])) {
	// only one value
	foreach ($_POST["upd_"] as $k => $v) { break; };
	if ($v == "Append") {
		$db->append_card($k,
			$_POST["c_rid"][$k],
			$_POST["c_card"][$k],
			(isset($_POST["c_formatted"][$k])
			?$_POST["c_formatted"][$k]:false));
	} else {
		// get one_batch info
		$onebatch = new OneBatch("[$k]");
		$ops = $onebatch->get_ops_batch();
		$bid = $onebatch->get_one_batch();
		$rid = $_POST["c_rid"][$k];
		$id = ( $rid ? $rid : $k);
		if ($ops == "" || $ops == "NONE") {
			$db->update_card($k,
				$_POST["c_rid"][$k],
				array(	"num"=>$_POST["c_num"][$k],
				"title"=>$_POST["c_title"][$k],
				"card"=>$_POST["c_card"][$k],
				"formatted"=>
					(isset($_POST["c_formatted"][$k])
					?$_POST["c_formatted"][$k]:false)
				));
		} elseif ($ops == "COPY") {
			$db->copy_card($id,$bid);
		} elseif ($ops == "RELATE") {
			$db->insert_card($bid, $id);
		} elseif ($ops == "MOVE") {
			$db->move_card($k,$bid);
		}
	}
}
// process a selective delete change
elseif (isset($_POST["del_"])
&& is_array($_POST["del_"])) {
	/* only one value */
	foreach ($_POST["del_"] as $k => $v) { break; };
	if ($v == "Delete" || $v == "Delete All") {
		$db->delete_card($k);
	}
}
// insert a card
elseif (isset($_POST["ins__"])
&& $_POST["ins__"]=="Insert") {
	$db->insert_card($_POST["one_batch_i"],
		array(	"num"=>$_POST["i_num"],
			"title"=>$_POST["i_title"],
			"card"=>$_POST["i_card"],
			"formatted"=>
				(isset($_POST["i_formatted"])
				?$_POST["i_formatted"]:false)
		),
		($user->level >= $level_write ? false : "append"));
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"]=="Batch Submit") {
	// get one_batch info
	$onebatch = new OneBatch("a");
	$ops = $onebatch->get_ops_batch();
	$bid = $onebatch->get_one_batch();
	foreach ($_POST["c_check"] as $k =>$v) {
		$id = $k;
		if ($ops == "DELETE") {
			$db->delete_card($id);
		} elseif ($ops == "COPY") {
			$db->copy_card($id,$bid);
		} elseif ($ops == "RELATE") {
			$db->insert_card($bid, $id);
		} elseif ($ops == "MOVE") {
			$db->move_card($k,$bid);
		}
	}
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"]=="Check All") {
	$check_all = true;
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"]=="Uncheck All") {
	$check_all = false;
}

if (0) {
// get cards from db
$cards = $db->xml_card_batchs($user->bids);
//$view->sort($cards);
//$db->dumper($cards);

$hhead = sendhelp("{$user->project} - Card View","card view");

$blist = "";
foreach ($user->bids as $k => $v) {
	if ($v["selected"]) {
		$blist .= " ".senddesc($v["batch"],$k,"batch")."\n";
	}
}
$bselect_top = "";
$bselect_bot = "";
if ($view->is_edit()
&& $user->level >= $level_write) {
	$abatch = new OneBatch("a");
	$bselect_top =	row(head(sendhelp("Help","batch ops")
			.$abatch->string_ops_batch(true)
			.$abatch->js_string_one_batch()
			.input("submit","a_submit","Batch Submit")))
			.row(head(input("submit","a_submit","Check All")
			.input("submit","a_submit","Uncheck All")))."\n";
	$bselect_bot =	row(head(sendhelp("Help","batch ops")
			.input("submit","a_submit","Batch Submit")))."\n";
	$js_ = javascript($js_RCM.$abatch->js_one_array().$js_loadSelect);
} else {
	$js_ = "";
}
	card_head("{$user->project} - View Cards","main",1,$js_);
	print $view->xml_settings();

$x = "<!--{-->".table(
	row(head($hhead))
	.(isset($errmsg)?row(head(warn($errmsg))):"")
	.row(cell("Batches: $blist","class=\"h_batch\""))
	.$bselect_top
	.row(cell($view->string_cards($cards,$check_all)))
	.$bselect_bot
,"class=\"outer\"")."<!--}-->";

if ($view->is_edit()) {
	print form($_SERVER['PHP_SELF'],$x);
} else {
	print $x."\n";
}
}
	xcard_head();
	print $view->xml_settings("  ");
	print $db->xml_card_batches($user->bids,true,"  ");
//	showphpinfo();
	xmlshowphpinfo(INFO_VARIABLES|INFO_ENVIRONMENT);
	xcard_foot();
?>
