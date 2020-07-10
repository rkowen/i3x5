<?php
// DESC: view the individual cards as selected by the batch
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";
	include_once "javascript.inc";

$debug = 0;
$cardsrerun = 0;

if ((! isset($view) || ! $user->selected_count()) 
&&  (! isset($view->cards))
&&  (! isset($_GET["bid"]))) {
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
	$db->lastcardsql($view->lastcardsql());
//
// process any changes
//

// process a global change only for real cards (not relations)
$db->debug($debug);
if (isset($_POST["upd_all"])
&& $_POST["upd_all"]=="Update All Cards"
&& is_array($_POST["c_rid"])) {
	if ($debug) { print("##### update all cards #####<br/>\n"); }
	reset($_POST["c_rid"]);
	while (list($k,$v) = each($_POST["c_rid"])) {
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
	$cardsrerun = 1;
}
// process a selective update change
elseif (isset($_POST["upd_"])
&& is_array($_POST["upd_"])) {
	if ($debug) { print("##### update one card #####<br/>\n"); }
	// only one value
	reset($_POST["upd_"]);
	list($k,$v) = each($_POST["upd_"]);
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
		$cardsrerun = 1;
	}
}
// process a selective delete change
elseif (isset($_POST["del_"])
&& is_array($_POST["del_"])) {
	if ($debug) { print("##### delete one card #####<br/>\n"); }
	reset($_POST["del_"]);
	/* only one value */
	list($k,$v) = each($_POST["del_"]);
	if ($v == "Delete" || $v == "Delete All") {
		$db->delete_card($k);
	}
	$cardsrerun = 1;
}
// insert a card
elseif (isset($_POST["ins__"])
&& $_POST["ins__"]=="Insert") {
	if ($debug) { print("##### insert one card #####<br/>\n"); }
	$db->insert_card($_POST["one_batch_i"],
		array(	"num"=>$_POST["i_num"],
			"title"=>$_POST["i_title"],
			"card"=>$_POST["i_card"],
			"formatted"=>
				(isset($_POST["i_formatted"])
				?$_POST["i_formatted"]:false)
		),
		($user->level >= $level_write ? false : "append"));
	$cardsrerun = 1;
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"] == "Batch Submit") {
	if ($debug) { print("##### batch submit  #####<br/>\n"); }
	// get one_batch info
	$onebatch = new OneBatch("a");
	$ops = $onebatch->get_ops_batch();
	$bid = $onebatch->get_one_batch();
	if ($debug) { print("##### ops=$ops bid=$bid  #####<br/>\n"); }
	reset($_POST["c_check"]);
	while (list($k,$v) = each($_POST["c_check"])) {
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
	$cardsrerun = 1;
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"]=="Check All") {
	if ($debug) { print("##### check all  #####<br/>\n"); }
	$check_all = true;
}
elseif (isset($_POST["a_submit"])
&& $_POST["a_submit"]=="Uncheck All") {
	if ($debug) { print("##### uncheck all  #####<br/>\n"); }
	$check_all = false;
}

// get cards from db
if (isset($view->cards)) {
	if ($cardsrerun) {
		$view->cards = $db->cards_rerun();
	}
	if ($debug) { print("##### cards from search  #####<br/>\n"); }
	$cards = $view->cards;
	// find all bids and sort them
	$cbids = array();
	reset($cards);
	while(list($k,$v) = each($cards)) {
		$i = $v['bid'];
		$cbids[$i] = (isset($cbids[$i])?$cbids[$i] + 1 : 1);
	}
	ksort($cbids);
	// reset the bids selected to those found in search
	reset($user->bids);
	foreach($user->bids as $k => $v) {
		$user->bids[$k]["selected"] = 0;
	}
	reset($cbids);
	foreach($cbids as $cbid => $cnt) {
		if ($cnt) {
			$user->bids[$cbid]["selected"] = 1;
		}
	}
} else {
// (Get the cards from all selected batches)
	if ($debug) { print("##### selected batches  #####<br/>\n"); }
	reset($user->bids);
	while (list($k,$v) = each($user->bids)) {
		if (isset($user->bid)) {
			// only one batch selected from list
			if ($user->bid == $k) {
				$user->bids[$k]["selected"] = 1;
			} else {
				if ($v["selected"]) {
					$user->bids[$k]["selected"] = 0;
				}
			}
		}
	}
	$cards = $db->cards_($user->bids);
	if (isset($user->bid)) {
		// clear bid if set
		//$user->bids[$user->bid]["selected"] = 0;
		unset($user->bid);
	}
}
$view->sort($cards);
//$db->dumper($cards);

$hhead = sendhelp("{$user->project} - Card View","card view");

$blist = "";
reset($user->bids);
while (list($k,$v) = each($user->bids)) {
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
	.row(cell(
span("","id=\"togcard_def\" class=".(($view->body == "full")
	? "shown" : "hidden"))
.button("pageprint","Print",
	"class=\"nonprint button\" onclick=\"javascript:printpage()\"")
.button("togcard",
span(span((($view->body == "full") ? "Hide" : "Show"),"id=\"togcard\"")
	." All ","class=\"noprint\""),
"class=\"nonprint\" onclick=\"javascript:hidecardall('togcard')\"")
."Batches: $blist","class=\"h_batch\""))
	.$bselect_top
	.row(cell($view->string_cards($cards,$check_all)))
	.$bselect_bot
,"class=\"outer\"")."<!--}-->";

if ($view->is_edit()) {
	print form($_SERVER['PHP_SELF'],$x);
} else {
	print $x."\n";
}
	showphpinfo();
if ($debug) {
	print "<pre style='text-align: left;'>\n";
	print("_POST : ");
	print_r($_POST);
	print("_SESSION : ");
	print_r($_SESSION);
	print("cards : ");
	print_r($user->cards);
//	print_r($user->bids);
	print "</pre>\n";
}
	card_foot();
?>
