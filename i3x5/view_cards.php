<?php
// DESC: view the individual cards as selected by the batch
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }
	$check_all = false;

if (! $view) {
	$view = new View();
}

//
// catch any GETS
//
	$view->catch_gets();
//
// process any changes
//

// process a global change only for real cards (not relations)
if (array_key_exists("upd_all", $_POST)
&& $_POST["upd_all"]=="Update All Cards"
&& is_array($_POST["c_rid"])) {
	reset($_POST["c_rid"]);
	while (list($k,$v) = each($_POST["c_rid"])) {
		if (! $v) {
			if ($user->level >= $level_write) {
				$db->update_card($k,$v,
				array(
				"num"=>$_POST["c_num"][$k],
				"title"=>$_POST["c_title"][$k],
				"card"=>$_POST["c_card"][$k],
				"formatted"=>$_POST["c_formatted"][$k]
				));
			} else { 	// append only
				$db->append_card($k, $v,
					$_POST["c_card"][$k]);
			}
		}
	}
}
// process a selective update change
elseif (array_key_exists("upd_", $_POST)
&& is_array($_POST["upd_"])) {
	// only one value
	reset($_POST["upd_"]);
	list($k,$v) = each($_POST["upd_"]);
	if ($v == "Append") {
		$db->append_card($k,
			$_POST["c_rid"][$k],
			$_POST["c_card"][$k]);
	} else {
		// get one_batch info
		$onebatch = new OneBatch("[$k]");
		$ops = $onebatch->get_ops_batch();
		$bid = $onebatch->get_one_batch();
		$rid = $_POST["c_rid"][$k];
		$id = ( $rid ? $rid : $k);
		if ($ops == "NONE") {
			$db->update_card($k,
				$_POST["c_rid"][$k],
				array(	"num"=>$_POST["c_num"][$k],
				"title"=>$_POST["c_title"][$k],
				"card"=>$_POST["c_card"][$k],
				"formatted"=>$_POST["c_formatted"][$k]
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
elseif (array_key_exists("del_", $_POST)
&& is_array($_POST["del_"])) {
	reset($_POST["del_"]);
	/* only one value */
	list($k,$v) = each($_POST["del_"]);
	if ($v == "Delete" || $v == "Delete All") {
		$db->delete_card($k);
	}
}
// insert a card
elseif (array_key_exists("ins__", $_POST)
&& $_POST["ins__"]=="Insert") {
	$db->insert_card($_POST["one_batch_i"],
		array(	"num"=>$_POST["i_num"],
			"title"=>$_POST["i_title"],
			"card"=>$_POST["i_card"],
			"formatted"=>$_POST["i_formatted"]
		),
		($user->level >= $level_write ? false : "append"));
}
elseif (array_key_exists("a_submit", $_POST)
&& $_POST["a_submit"]=="Batch Submit") {
	// get one_batch info
	$onebatch = new OneBatch("a");
	$ops = $onebatch->get_ops_batch();
	$bid = $onebatch->get_one_batch();
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
}
elseif (array_key_exists("a_submit", $_POST)
&& $_POST["a_submit"]=="Check All") {
	$check_all = true;
}
elseif (array_key_exists("a_submit", $_POST)
&& $_POST["a_submit"]=="Uncheck All") {
	$check_all = false;
}

// get cards from db
$cards = $db->cards($user->bids);
$view->sort($cards);
//$db->dumper($cards);

$hhead = sendhelp("{$user->project} - Card View","card view");

	print <<<PAGE
<HTML>
<HEAD>
<TITLE>{$user->project} - View Cards</TITLE>
<BODY $result_bg>
<CENTER>
<!--{-->
<FORM ACTION="$PHP_SELF" METHOD="POST">
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color" WIDTH=100%>
<TR><TH> $hhead </TH></TR>

PAGE;
	if (isset($errmsg)) {
		print row(head(warn($errmsg)));
	}
	print "<TR><TD> Batches: <B>";
	reset($user->bids);
	while (list($k,$v) = each($user->bids)) {
		if ($v["selected"]) {
			print " ".senddesc($v["batch"],$k,"batch")."\n";
		}
	}
	print "</B></TD></TR>\n";
	if ($view->is_edit()) {
	$abatch = new OneBatch("a");
	print	row(head(sendhelp("Help","batch ops")
			.$abatch->string_ops_batch(true)
			.$abatch->string_one_batch(false)
			.input("submit","a_submit","Batch Submit")))
		.row(head(input("submit","a_submit","Check All")
			.input("submit","a_submit","Uncheck All")))
		."<TR><TH>\n";
	}
	$view->cards($cards,$check_all);
	if ($view->is_edit()) {
	print "</TH></TR>\n"
		.row(head(sendhelp("Help","batch ops")
			.input("submit","a_submit","Batch Submit")));
	}
	print <<<PAGE
</TABLE>
<!--}-->
</FORM>
</CENTER>
PAGE;
	if ($phpinfo) {phpinfo();}
	print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
