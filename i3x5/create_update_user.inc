<?php
//
// DESC: create_update_user class to handle user info processing
//

include_once "cards.inc";

class Create_Update_User {
	var $type;	// Create or Update
	var $invalid;	// number of invalid entries
	var $list;	// list of entries

// constructor
function Create_Update_User($type) {

	$this->type = $type;
	$this->invalid = 0;

	$this->list = array(
	"username"	=> array(
		"label"	=>"User Name",
		"check"	=> "validate_username",
		"maxlen"=> 25,
		"msg"	=> ""),
	"project"	=> array(
		"label"	=> "Project Name",
		"check"	=> "validate_length",
		"maxlen"=> 25,
		"msg"	=> ""),
	"passwd_admin"	=> array(
		"label"	=> "Password - Admin",
		"check"	=> "validate_strong",
		"maxlen"=> 25,
		"msg"	=> ""),
	"passwd_w"	=> array(
		"label"	=> "Password - Write",
		"check"	=> "validate_strong",
		"maxlen"=> 25,
		"msg"	=> ""),
	"passwd_a"	=> array(
		"label"	=> "Password - Append only",
		"check"	=> "validate_weak",
		"maxlen"=> 25,
		"msg"	=> ""),
	"passwd_r"	=> array(
		"label"	=> "Password - Read only",
		"check"	=> "validate_weak",
		"maxlen"=> 25,
		"msg"	=> ""),
	"author"	=> array(
		"label"	=> "Name - Admin",
		"check"	=> "validate_name",
		"maxlen"=> 25,
		"msg"	=> ""),
	"email"		=> array(
		"label"	=> "Email - Admin",
		"check"	=> "validate_email",
		"maxlen"=> 25,
		"msg"	=> ""),
	"challenge"	=> array(
		"label"	=> "Challenge Prompt",
		"check"	=> "validate_length",
		"maxlen"=> 60,
		"msg"	=> ""),
	"response"	=> array(
		"label"	=> "Challenge Response",
		"check"	=> "validate_length",
		"maxlen"=> 40,
		"msg"	=> "")
	);
}

// methods

// converts \'s to '            -- for select outputs
function dequote($q) {
	return preg_replace("/\\\\'/","'",$q);
}

function validate_name($q, &$msg ) {

	if (strlen($q) < 3) {
		$msg = cell(warn("Please give your name"));
		return 0;
	}
	return 1;
}

function validate_email($q, &$msg ) {
	if (!preg_match("/[^@]{1,}@[^\.]{1,}\.[a-zA-Z0-9_-]{1,}/",$q)){
		$msg = cell(warn("Please give your email address"));
		return 0;
	}
	return 1;
}

// Must be of some length
function validate_length($q, &$msg, $num = 3) {

	if (strlen($q) < $num) {
		$msg = cell(warn("must be at least $num characters"));
		return 0;
	}
	return 1;
}

// no white space is allowed in the text
function validate_text($q, &$msg, $num = 3) {

	if (count(preg_split("/\s/",$q)) > 1) {
		$msg = cell(warn("No white space allowed"));
		return 0;
	}
	if (! $this->validate_length($q, $msg, $num)) { return 0; }
	return 1;
}

// no white space is allowed in the username
function validate_username($q, &$msg) {

	// can't change the username if doing an update
	if ($this->type == "Update") {
		$msg = cell(inform("Can't change username"));
		return 1;
	}
	if (! $this->validate_text($q, $msg, 3)) { return 0; }

	global $db;
	// check if username is already in use
	if ($db->sql(
		"SELECT count(*) FROM i3x5_userpass WHERE username='".
		$_POST["username"]."'")) {
		$msg = cell(warn(
		"This user name already exists please choose another"));
		return 0;
	}
	return 1;
}

// allow a weaker password for the append & read users
function validate_weak($q, &$msg) {
	if (! $this->validate_text($q, $msg, 6)) { return 0; }
	if (count(preg_split("/[A-Z0-9]/",$q)) < 3) {
		$msg = cell(warn(
			"At least 2 capitol letters or numbers"));
		return 0;
	}
	return 1;
}

// enforce a stronger password for the admin & write users
function validate_strong($q, &$msg) {
	if (! $this->validate_weak($q, $msg)) { return 0; }
	$q = preg_replace("/[^a-z]/",":",$q);
	if (count(split(':',$q)) < 4) {
		$msg = cell(warn(
		"Must have at least 3 non-lower case characters"));
		return 0;
	}
	return 1;
}


//------------ active code --------------

function get_form() {

	if ($_POST["clear"]) {
		// clear the posted values
		reset($this->list);
		while(list($k,$v) = each($this->list)) {
			// clear all but username if update
			if ($this->type != "Update" || $k != "username") {
				$_POST[$k] = "";
				$$k = "";
			}
		}
	}
	// clean-up values
	reset($this->list);
	while(list($k,$v) = each($this->list)) {
		if ($_POST[$k]) {
			$$k = $this->dequote(
				strip_tags(trim($_POST[$k])));
			$_POST[$k] = $$k;
		}
	}

	// validate the input fields
	$this->invalid = 0;
	reset($this->list);
	while(list($k,$v) = each($this->list)) {
		$check = $this->list[$k]["check"];
		if (! $this->$check($$k, $this->list[$k]["msg"])) {
			$this->invalid++;
		}
	}
	
	if ($this->invalid) {
		$_POST["create_update_user"] = "Invalid";
	}
}

//------------ non-active code --------------

function show_form($title, $helpkey, $url="indexM.php") {
	global $result_bg;
	global $box_color;
	global $form_color;
	global $PHP_SELF;

	$thelp = sendhelp($title,$helpkey);
	print <<<EOT
<HTML>
<HEAD>
<TITLE> $title </TITLE>
</HEAD>
<BODY $result_bg>
<CENTER>
<TABLE ALIGN="center" BORDER=1 CELLPADDING=10 CELLSPACING=0 BGCOLOR="$box_color">
<TR><TH><A HREF="$url"><IMG SRC="back.gif" ALT="back up" BORDER="0"></A></TH>
<TH><CENTER><BIG> $thelp </BIG></CENTER>
</TH></TR> <TR><TH COLSPAN=2>
	<FORM ACTION="$PHP_SELF" METHOD="POST">
	<TABLE BORDER=1 CELLPADDING=2 CELLSPACING=2 BGCOLOR="$form_color">
EOT;

reset($this->list);
while(list($k,$v) = each($this->list)) {
	$label = sendhelp($this->list[$k]["label"],$k);
	$msg = $this->list[$k]["msg"];
	$ml = $this->list[$k]["maxlen"];

	if ($this->type=="Update" && $k == "username") {
		print row(cell($label)."\n	".
		cell("<INPUT NAME=\"$k\" TYPE=\"hidden\" VALUE=\"".
			$_POST[$k]."\">".$_POST[$k]
			). $msg)."\n";
	} else {
		print row(cell($label)."\n	".
		cell("<INPUT NAME=\"$k\" SIZE=\"25\" MAXLENGTH=\"$ml\" TYPE=\"text\" ".
		"VALUE=\"{$_POST[$k]}\">"). $msg)."\n";
	}
}

print <<<EOT
	<TR><TH COLSPAN=3>
	<INPUT NAME="submit"			TYPE="submit"	value="{$this->type}" >
	<INPUT NAME="reset"			TYPE="reset"	value="Reset" >
	<INPUT NAME="clear"			TYPE="submit"	value="Clear" >
	<INPUT NAME="create_update_user"	TYPE="hidden"	value="Done" >
	</TD></TR>
	</TABLE>
	</FORM>
</TH></TR>
</TABLE>
EOT;
// need to add end stuff

	if ($this->invalid) {
		print warn(
"There were {$this->invalid} field(s) that need to be corrected")."<BR>\n";
	}
}

}

?>
