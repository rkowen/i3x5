<?php
// DESC: create a batch or update batch properties
	include_once "cards.inc";
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "3x5_db.inc";
	include_once "one_batch.inc";

	$insert = true;			// governs how data is input to table
	$relmsg = "";			// unneeded actually
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }
 
	// list of fields
	$list = array(
	"name"	=> array(
		"label"	=> "Batch name",
		"check"	=> "validate_name",
		"maxlen"=> 25,
		"msg"=> ""),
	"number"=> array(
		"label"	=> "Number field name",
		"check"	=> "validate_text",
		"maxlen"=> 25,
		"msg"=> ""),
	"title"	=> array(
		"label"	=> "Title field name",
		"check"	=> "validate_text",
		"maxlen"=> 25,
		"msg"=> ""),
	"card"	=> array(
		"label"	=> "Card field  name",
		"check"	=> "validate_text",
		"maxlen"=> 25,
		"msg"=> ""),
	);

	// validation functions
	function validate_text( $q, &$msg ) {
		global $non_blank;
		if (strlen($q) == 0) {
			$msg = cell(warn("blank fields are ignored"));
			// but it's OK
		} else {
			$non_blank = 1;
		}
		return 1;
	}

	function validate_name( $q, &$msg ) {
		global $db;
		global $user;
		global $bid;
		global $insert;
		global $batch_select;
		// check if name is in DB
		// make sure batch is not already being used by the user
		// warn the user if it is ... they may want to overwrite it
		$bid = $db->sql(
		"SELECT bid FROM i3x5_batch WHERE uid={$user->uid} AND batch='".
			$_POST["name"]."'");
		if ($bid) {
			if ("Update" == $batch_select) {
				$msg = cell(warn(
			"may update a pre-existing batch name"));
				$insert = false;
				return 1;	// it's OK anyways
			} else {
				$msg = cell(warn(
			"pre-existing batch name ... needs to be unique"));
				$insert = false;
				return 0;	// not OK
			}
		}
		$insert = true;
		return 1;
	}

//-----------------------------------------------------
// if selected a batch
	$onebid = new OneBatch();
	$one_batch__=$onebid->get_one_batch();	// retrieves $one_batch__

//-----------------------------------------------------
// set name if GET
if (isset($_GET["name"])) {
	$_POST["name"] = $_GET["name"];
}
if (isset($_GET["name_help"])) {
	$_POST["name_help"] = $_GET["name_help"];
}
if (isset($_GET["batch_select"])) {
	$_POST["batch_select"] = $_GET["batch_select"];
}
if (isset($_POST["batch_select"])) {
	$batch_select = $_POST["batch_select"];
}
if (isset($_GET["bid"]) && ! isset($_POST["bid"])) {
	$_POST["bid"] = $_GET["bid"];
}
if (isset($_POST["bid"])) {
	$bid = $_POST["bid"];
}
if (isset($_POST["copy_relate"])) {
	$copy_relate = $_POST["copy_relate"];
	if ($copy_relate == "None") {
		unset($copy_relate);
	}
}

//-----------------------------------------------------
// If not set ... then must be New
	if (! isset($batch_select)) {
		$batch_select = "New";
		$suggest_card = 1;
	}
//-----------------------------------------------------
// check if example (overrides everything)
if (isset($_GET["example"])) {
	if ($_GET["example"] == "card") {
// print "-----card<BR>\n";
		$_POST["name"] = $_GET["name"];
		$_POST["name_help"] = $_GET["name_help"];
		$_POST["number"] = "Number";
		$_POST["title"] = "Title";
		$_POST["card"] = "Card";
		$_POST["number_help"] =
			"For ordering the cards numerically";
		$_POST["title_help"] = "Title for card";
		$_POST["card_help"] = "Card of info";

	} elseif ($_GET["example"] == "journal") {
// print "-----journal<BR>\n";
		$_POST["number"] = "Date";
		$_POST["title"] = "Entry";
		$_POST["card"] = "Journal";
		$_POST["number_help"] = "Date in YYYYMMDD format";
		$_POST["title_help"] = "Short description of day";
		$_POST["card_help"] = "Detailed daily journal entry";

	} elseif ($_GET["example"] == "people") {
// print "-----people<BR>\n";
		$_POST["number"] = "";
		$_POST["title"] = "Name";
		$_POST["card"] = "Info";
		$_POST["title_help"] =
			"Name of person last name, first name";
		$_POST["card_help"] =
			"Address, Phone, Misc Information";
	} elseif ($_GET["example"] == "recipe") {
// print "-----recipe<BR>\n";
		$_POST["number"] = "Calories";
		$_POST["title"] = "Recipe";
		$_POST["card"] = "Ingredients";
		$_POST["batch_help"] = "Recipe Classification";
		$_POST["number_help"] = "Calories per serving";
		$_POST["title_help"] = "Common Recipe Name";
		$_POST["card_help"] =
			"Recipe Ingredients and Instructions";
	}
} else {
	// we either got a bid or a name
	// check if called from batches.php directly
	// find bid & set up fields
	if (isset($_GET["name"])) {
		// not given bid already
		if (! $_GET["bid"]) {
			$bid = $db->sql(
	"SELECT bid FROM i3x5_batch WHERE uid={$user->uid} AND batch='".
				$_GET["name"]."'");
		} else {
			$bid = $_GET["bid"];
		}
		// may still not have proper bid
		if ($bid) {
			$fn = $db->batch_fieldnames($bid);
			$_POST["number"] = $fn["num"];
			$_POST["title"] = $fn["title"];
			$_POST["card"] = $fn["card"];
			$_POST["batch_help"] = (isset($fn["batch_help"]) ?
				$fn["batch_help"] : "");
			$_POST["number_help"] = $fn["num_help"];
			$_POST["title_help"] = $fn["title_help"];
			$_POST["card_help"] = $fn["card_help"];
			$_GET["example"] = "none";
		}
	}

	// get field names (from related batch if need to)
	// is this necessary?
	if ( 0 && $bid > 0 && ($batch_select=="Update")) {
		$rbid = $db->sql("SELECT rid FROM i3x5_batch WHERE bid=$bid");
		if ($rbid > 0) {
			$batch_select="Related";
			$relmsg = cell(inform("Currently Related to '".
				$user->bids[$rbid]["batch"]."'"));
		} else {
			$relmsg = "";
		}
		$fn = $db->batch_fieldnames($bid);
		$_POST["number"] = $fn["num"];
		$_POST["title"] = $fn["title"];
		$_POST["card"] = $fn["card"];
		$_POST["batch_help"] = $fn["batch_help"];
		$_POST["number_help"] = $fn["num_help"];
		$_POST["title_help"] = $fn["title_help"];
		$_POST["card_help"] = $fn["card_help"];
	}

	// if Copy or Relate ... then get those values stored away
	if ($one_batch__ && isset($copy_relate)){
		$fn = $db->batch_fieldnames($one_batch__);
		$_POST["number"] = $fn["num"];
		$_POST["title"] = $fn["title"];
		$_POST["card"] = $fn["card"];
		$_POST["batch_help"] = $fn["batch_help"];
		$_POST["number_help"] = $fn["num_help"];
		$_POST["title_help"] = $fn["title_help"];
		$_POST["card_help"] = $fn["card_help"];
		$_POST["rid"] = $fn["bid"];
			// which bid to relate to
	}
}

//---------------------------------------------------
// see which radio button should be CHECKED as to type and define helps
	$Check_None = "";
	$Check_Copy = "";
	$Check_Relate = "";
	if (isset($copy_relate)) {
		$check = "Check_".$copy_relate;
		$$check = "CHECKED";
	}
	if ($batch_select == "New") {
		$header = "{$user->project} - Create New Batch";
		$hhelp = sendhelp($header,"create batch");
		$hthis = sendhelp("New","create batch");
	} else {
		$header = "{$user->project} - Update Existing Batch";
		$hhelp = sendhelp($header,"update batch");
		$hthis= sendhelp("Update","update batch") . " ($bid)";
	}
	$hcopy = sendhelp("Copy","batch copy");
	$hrelate = sendhelp("Relate","batch relate");
//------------ clear fields (if asked) --------------
	if (isset($_POST["clear"])) {
		// clear the posted values
		reset($list);
		while(list($k,$v) = each($list)) {
			// clear all but name
			if ($k != "name") {
				$_POST[$k] = "";
				$_POST[$k."_help"] = "";
				$$k = "";
			}
		}
	}
//------------ clean-up and set local values --------------
	reset($list);
	while(list($k,$v) = each($list)) {
		if ($_POST[$k]) {
			$$k = strip_tags(trim($_POST[$k]));
			$_POST[$k] = $$k;
		}
	}
//------------ validate the input fields --------------
	// we have to have at least one that is a non-blank field
	$non_blank = 0;
	$invalid = 0;
	reset($list);
	while(list($k,$v) = each($list)) {
		$check = $list[$k]["check"];
		if (! $check($$k, $list[$k]["msg"])) { $invalid++; }
	}
	if (!$non_blank) {
		$_POST["create_batch"] = "Invalid";
	}

//{------------ update/insert to DB --------------
if (isset($_POST["create_batch"])) {
if ("New" == $_POST["create_batch"]) {
// looks OK ... add it into DB
	if ($one_batch__ && isset($copy_relate) && $copy_relate == "Relate") {
		$sql = 
"INSERT INTO i3x5_batch (uid,batch,batch_help,rid) VALUES (".
$user->uid.",'".
$db->escape($_POST["name"])."','".
$db->escape($_POST["name_help"])."',".
$_POST["rid"].")";
		$sqlmsg = "Batch was inserted as relation";
	} else {
		$sql = 
"INSERT INTO i3x5_batch (uid,batch,num_name,title_name,card_name,\n".
"batch_help,num_help,title_help,card_help) VALUES (\n".
$user->uid.",'".
$db->escape($_POST["name"])."','".
$db->escape($_POST["number"])."','".
$db->escape($_POST["title"])."','".
$db->escape($_POST["card"])."','".
$db->escape($_POST["name_help"])."','".
$db->escape($_POST["number_help"])."','".
$db->escape($_POST["title_help"])."','".
$db->escape($_POST["card_help"])."')";
		$sqlmsg = "Batch was added";
	}
} elseif ("Update" == $_POST["create_batch"]) {
	if ($one_batch__ && $copy_relate == "Relate") {
		$sql = 
"UPDATE i3x5_batch SET ".
"batch='".$db->escape($_POST["name"])."',".
"batch_help='".$db->escape($_POST["name_name"])."',".
"rid=".$_POST["rid"]." WHERE ".
"bid=".$bid." AND ".
"uid=".$user->uid;
		$sqlmsg = "Batch was updated as relation";
	} else {
		$sql = 
"UPDATE i3x5_batch SET ".
"batch='".$_POST["name"]."',".
"rid=NULL,".
"num_name='".$db->escape($_POST["number"])."',".
"title_name='".$db->escape($_POST["title"])."',".
"card_name='".$db->escape($_POST["card"])."',".
"batch_help='".$db->escape($_POST["name_help"])."',".
"num_help='".$db->escape($_POST["number_help"])."',".
"title_help='".$db->escape($_POST["title_help"])."',".
"card_help='".$db->escape($_POST["card_help"])."' WHERE ".
"bid=".$bid." AND ".
"uid=".$user->uid;
		$sqlmsg = "Batch was updated";
	}
}
	if (! $invalid ) {
		$db->sql($sql);
		// need to update list of bids
		$user->update_bids($db->bids($user->uid));
	} else {
		$sqlmsg = warn("Please fix the invalid entries before retrying");
	}
}
//}

print <<<PAGE
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css">
<title>$header</title>
</head>
<body class="main">
<center>
PAGE;

$hbatch = sendhelp("Batch<BR>Property","batch property");
$hlabel = sendhelp("Label","batch label");
$hhelp = sendhelp("Helpful Description","batch help");

print <<<PAGE
<!--{-->
<table class="tight">
<tr><th>$hhelp</th></tr>
<tr><th>
	<form action="new_batch.php" method="POST">
	<!--{-->
	<table border=1 cellpadding=2 cellspacing=2 bgcolor="$form_color">
	<tr><th colspan=3>
		<!--{-->
PAGE;
print table(row(cell(
	$hthis.input("radio","copy_relate","None",$Check_None)
		,"class=\"b_form\"")
	.cell(table("<!--{-->".row(cell(
		input("radio","copy_relate","Copy",$Check_Copy).$hcopy
		.input("radio","copy_relate","Relate",$Check_Relate).$hrelate
		.$onebid->string_one_batch()
	)),"class=\"form\"")."<!--}-->\n"))
,"class=\"form\" border=1")."<!--}-->\n";
print "</th>".row(
	cell($hbatch,"class=\"h_form\"")
	.cell($hlabel,"class=\"h_form\"")
	.cell($hhelp,"class=\"h_form\""))."\n";

reset($list);
while(list($k,$v) = each($list)) {
	$label = sendhelp($list[$k]["label"],"batch ".$k);
	$msg = $list[$k]["msg"];
	$ml = $list[$k]["maxlen"];

	print row(cell($label,"class=\"h_form\"")."\n	"
	.cell(input("text",$k,$_POST[$k],"size=18 maxlength=$ml"))
	.cell(input("text",$k."_help",$_POST[$k."_help"],
		"size=40 maxlength=200")) .$msg)."\n";
}

print	row(head(
		input("submit","create_batch",$batch_select)
		.input("submit","check","Check")
		.input("reset","reset","Reset")
		.input("submit","clear","Clear")
		.input("hidden","bid",$bid)
		.input("hidden","batch_select",$batch_select)
	,"colspan=3"))."\n</table><!--}-->\n</form>\n</th></tr>\n";

if (! $non_blank) {
	if (! isset($sqlmsg)) {
		$sqlmsg = "";
	}
	$sqlmsg = warn(
		"You must have at least 1 non-blank {$user->project} field\n")
		.$sqlmsg;
}

if (isset($sqlmsg)) {
	print row(cell(inform("<H2>".$sqlmsg."</H2>")));
}

$url= "new_batch.php?name=".urlencode($_POST["name"])
	."&name_help=".urlencode($_POST["name_help"])
	."&batch_select=".$batch_select;
	if ($batch_select == "Update" && isset ($bid)) {
		$url .= "&bid=$bid";
	}
	$url .= "&example";

print row(cell(
	"\nEmpty fields will not be shown in {$user->project}<br>\n"
	.sendhelp("Card","card example")
	." <a href=\"$url=card\">".inform("Example")."</a> (default)<br>\n"
	.sendhelp("Journal","journal example")
	." <a href=\"$url=journal\">".inform("Example")."</a><br>\n"
	.sendhelp("People","people example")
	." <a href=\"$url=people\">".inform("Example")."</a><br>\n"
	.sendhelp("Recipe","recipe example")
	." <a href=\"$url=recipe\">".inform("Example")."</a><br>\n"
,"id=\"left\""))."\n</table><!--}-->\n";
	showphpinfo();
print <<<PAGE
</center>
</body>
</html>
PAGE;

?>
