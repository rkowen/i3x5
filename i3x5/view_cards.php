<?php
// DESC: view the individual cards as selected by the batch
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

//
// process any changes
//

// process a global change only for real cards (not relations)
if ($HTTP_POST_VARS["upd_all"]=="Update All Cards"
    && is_array($HTTP_POST_VARS["c_rid"])) {
	reset($HTTP_POST_VARS["c_rid"]);
	while (list($k,$v) = each($HTTP_POST_VARS["c_rid"])) {
		if (! $v) {
			if ($user->level >= $level_write) {
				$db->update_card($k,$v,
				array(
				"num"=>$HTTP_POST_VARS["c_num"][$k],
				"title"=>$HTTP_POST_VARS["c_title"][$k],
				"card"=>$HTTP_POST_VARS["c_card"][$k],
				"formatted"=>$HTTP_POST_VARS["c_formatted"][$k]
				));
			} else { 	// append only
				$db->append_card($k, $v,
					$HTTP_POST_VARS["c_card"][$k]);
			}
		}
	}
}
// process a selective update change
elseif (is_array($HTTP_POST_VARS["upd_"])) {
	// only one value
	reset($HTTP_POST_VARS["upd_"]);
	list($k,$v) = each($HTTP_POST_VARS["upd_"]);
	if ($v == "Append") {
		$db->append_card($k,
			$HTTP_POST_VARS["c_rid"][$k],
			$HTTP_POST_VARS["c_card"][$k]);
	} else {
		// get one_batch info
		$onebatch = new OneBatch("[$k]");
		$ops = $onebatch->get_ops_batch();
		$bid = $onebatch->get_one_batch();
		$rid = $HTTP_POST_VARS["c_rid"][$k];
		$id = ( $rid ? $rid : $k);
		if ($ops == "NONE") {
			$db->update_card($k,
				$HTTP_POST_VARS["c_rid"][$k],
				array(	"num"=>$HTTP_POST_VARS["c_num"][$k],
				"title"=>$HTTP_POST_VARS["c_title"][$k],
				"card"=>$HTTP_POST_VARS["c_card"][$k],
				"formatted"=>$HTTP_POST_VARS["c_formatted"][$k]
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
elseif (is_array($HTTP_POST_VARS["del_"])) {
	reset($HTTP_POST_VARS["del_"]);
	/* only one value */
	list($k,$v) = each($HTTP_POST_VARS["del_"]);
	if ($v == "Delete" || $v == "Delete All") {
		$db->delete_card($k);
	}
}
// insert a card
elseif ($HTTP_POST_VARS["ins__"]=="Insert") {
	$db->insert_card($HTTP_POST_VARS["one_batch_i"],
		array(	"num"=>$HTTP_POST_VARS["i_num"],
			"title"=>$HTTP_POST_VARS["i_title"],
			"card"=>$HTTP_POST_VARS["i_card"],
			"formatted"=>$HTTP_POST_VARS["i_formatted"]
		),
		($user->level >= $level_write ? false : "append"));
}

// get cards from db
$cards = $db->cards(&$user->bids);
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
	if ($errmsg) {
		print row(head(warn($errmsg)));
	}
	print "<TR><TD> Batches: <B>";
	reset($user->bids);
	while (list($k,$v) = each($user->bids)) {
		if ($v[selected]) {
			print " ".senddesc($v[batch],$k,"batch")."\n";
		}
	}
	print "</B></TD></TR><TR><TH>\n";
	$view->cards($cards);
	print "</TH></TR>\n";
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
