<?php
// DESC: create a batch or update batch properties
	include_once "cards.inc";
	include_once "user.inc";

	session_start();

	include_once "3x5_db.inc";
	include_once "one_batch.inc";

	$insert = true;			// governs how data is input to table
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
			$msg = cell(warn("this field will be blanked out"));
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
		// check if name is in DB
		// make sure batch is not already being used by the user
		// warn the user if it is ... they may want to overwrite it
		$bid = $db->sql(
		"SELECT bid FROM i3x5_batch WHERE uid={$user->uid} AND batch='".
			$_POST["name"]."'");
		if ($bid) {
			$msg = cell(warn(
		"pre-existing batch name, you may update"));
			$insert = false;
			return 1;	// it's OK anyways
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
if ($_GET["name"]) {
	$_POST["name"] = $_GET["name"];
	$name = $_GET["name"];
}
if ($_GET["name_help"]) {
	$_POST["name_help"] = $_GET["name_help"];
	$name_help = $_GET["name_help"];
}

//-----------------------------------------------------
// If not set ... then must be New
	if (! $batch_select ) {
		$batch_select = "New";
		$suggest_card = 1;
	}
//-----------------------------------------------------
// check if example (overrides everything)
if ($_GET["example"]) {
	if ($_GET["example"] == "card") {
// print "-----card<BR>\n";
		$_POST["name"] = $_GET["name"];
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
// print "-----people<BR>\n";
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
	if ($_GET["name"]) {
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
			$_POST["batch_help"] = $fn["batch_help"];
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
	if ($one_batch__ && ($batch_select=="Copy" || $batch_select=="Relate")){
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
// see which radio button should be CHECKED as to type
	$check = "Check_".$batch_select;
	$$check = "CHECKED";
	if ($Check_Update) { $Check_New = "CHECKED"; }
	if ($batch_select == "New") {
		$header = "{$user->project} - Create New Batch";
		$hhelp = sendhelp($header,"create batch");
		$button = "Create";
	} else {
		$header = "{$user->project} - Update Existing Batch";
		$hhelp = sendhelp($header,"update batch");
		$button = "Update";
	}
//------------ clear fields (if asked) --------------
	if ($_POST["clear"]) {
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
if ("Create" == $_POST["create_batch"]
||  "Update" == $_POST["create_batch"]) {
// looks OK ... add it into DB
	if ($batch_select == "New" || $batch_select == "Copy") {
		if ($insert) {
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
		} else {		// need to update instead
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
	} elseif ($one_batch__ && $batch_select == "Relate") {
		if ($insert) {
			$sql = 
"INSERT INTO i3x5_batch (uid,batch,batch_help,rid) VALUES (".
$user->uid.",'".
$db->escape($_POST["name"])."','".
$db->escape($_POST["name_help"])."',".
$_POST["rid"].")";
			$sqlmsg = "Batch was inserted as relation";
		} else {
			$sql = 
"UPDATE i3x5_batch SET ".
"batch='".$db->escape($_POST["name"])."',".
"batch_help='".$db->escape($_POST["name_name"])."',".
"rid=".$_POST["rid"]." WHERE ".
"bid=".$bid." AND ".
"uid=".$user->uid;
			$sqlmsg = "Batch was updated as relation";
		}
	}

	$db->sql($sql);
	// need to update list of bids
	$user->update_bids($db->bids($user->uid));
}
//}

print <<<PAGE
<HTML>
<HEAD>
<TITLE>$header</TITLE>
<BODY $result_bg>
<CENTER>
PAGE;

$back="batches.php";
if ($bid) {
	$back .= "?b_select=Update&one_batch__=$bid";
} else {
	$back .= "?b_select=New&new_batch=".urlencode($name);
}
$hcopy = sendhelp("Copy","batch copy");
$hrelate = sendhelp("Relate","batch relate");
print <<<PAGE
<!--{-->
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH><A HREF="$back"><IMG SRC="back.gif" ALT="back to properties" BORDER="0" ></A>
</TH><TH>$hhelp</TD></TR>
<TR><TH COLSPAN=2>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<!--{-->
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
	<TR><TH COLSPAN=3>
		<!--{-->
		<TABLE BORDER=1><TR><TD>
		<INPUT NAME="batch_select" $Check_New TYPE="radio" VALUE="New">
		New/Update</TD><TD>
		  <!--{-->
		  <TABLE><TR><TD>
		  <INPUT NAME="batch_select" $Check_Copy TYPE="radio" VALUE="Copy">
		  $hcopy </TD><TD>
		  <INPUT NAME="batch_select" $Check_Relate TYPE="radio" VALUE="Relate">
		  $hrelate </TD>
		  <TD>
PAGE;
$onebid->show_one_batch();
$hbatch = sendhelp("Batch<BR>Property","batch property");
$hlabel = sendhelp("Label","batch label");
$hhelp = sendhelp("Helpful Description","batch help");

print <<<PAGE
		  </TD></TR></TABLE><!--}-->
	</TD>$relmsg</TR></TABLE><!--}-->
</TH></TR>
<TR><TH>$hbatch</TH>
	<TH>$hlabel</TH><TH>$hhelp</TH></TR>

PAGE;

reset($list);
while(list($k,$v) = each($list)) {
	$label = sendhelp($list[$k]["label"],"batch ".$k);
	$msg = $list[$k]["msg"];
	$ml = $list[$k]["maxlen"];

	print row(cell($label)."\n	".
	cell("<INPUT NAME=\"$k\" SIZE=\"18\" MAXLENGTH=\"$ml\" TYPE=\"text\" ".
		"VALUE=\"{$_POST[$k]}\">")."\n"
	.cell("<INPUT NAME=\"{$k}_help\" SIZE=\"40\" MAXLENGTH=\"200\" TYPE=\"text\" ".
		"VALUE=\"{$_POST[$k."_help"]}\">").$msg)."\n";
}

print <<<PAGE
	<TR><TH COLSPAN=3>
	<INPUT NAME="create_batch"		TYPE="submit"	value="$button">
	<INPUT NAME="check"			TYPE="submit"	value="Check" >
	<INPUT NAME="reset"			TYPE="reset"	value="Reset" >
	<INPUT NAME="clear"			TYPE="submit"	value="Clear" >
	<INPUT NAME="bid"			TYPE="hidden"	value="$bid" >
	</TD></TR>
	</TABLE><!--}-->
	</FORM>
</TH></TR>
PAGE;

if ($sqlmsg) {
	print row(cell(inform("<H2>".$sqlmsg."</H2>"),"COLSPAN=2"));
}

$url= "$PHP_SELF?name=".urlencode($_POST["name"])
	."&name_help=".urlencode($_POST["name_help"])
	."&batch_select=".$batch_select
	."&example";

print "<TR><TD COLSPAN=2>\n";
print "Empty fields will not be shown in {$user->project}<BR>\n";
print sendhelp("Card","card example")
	." <A HREF=\"$url=card\">".inform("Example")."</A>(default)<BR>\n";
print sendhelp("Journal","journal example")
	." <A HREF=\"$url=journal\">".inform("Example")."</A><BR>\n";
print sendhelp("People","people example")
	." <A HREF=\"$url=people\">".inform("Example")."</A><BR>\n";
print sendhelp("Recipe","recipe example")
	." <A HREF=\"$url=recipe\">".inform("Example")."</A><BR>\n";

print "</TD></TR>\n";

if (! $non_blank) {
	print row(cell(warn(
	"You must have at least 1 non-blank {$user->project} field"),"COLSPAN=2"));
}

print <<<PAGE
</TABLE><!--}-->
PAGE;
	if ($phpinfo) {phpinfo();}
print <<<PAGE
</BODY>
</HTML>
PAGE;

?>
