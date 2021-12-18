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
		"maxlen"=> 50,
		"crypt"	=> false,
		"msg"	=> ""),
	"project"	=> array(
		"label"	=> "Project Name",
		"check"	=> "validate_length",
		"maxlen"=> 100,
		"crypt"	=> false,
		"msg"	=> ""),
	"passwd_admin"	=> array(
		"label"	=> "Password - Admin",
		"check"	=> "validate_strong",
		"maxlen"=> 50,
		"crypt"	=> true,
		"msg"	=> ""),
	"passwd_w"	=> array(
		"label"	=> "Password - Write",
		"check"	=> "validate_strong",
		"maxlen"=> 50,
		"crypt"	=> true,
		"msg"	=> ""),
	"passwd_a"	=> array(
		"label"	=> "Password - Append only",
		"check"	=> "validate_weak",
		"maxlen"=> 50,
		"crypt"	=> true,
		"msg"	=> ""),
	"passwd_r"	=> array(
		"label"	=> "Password - Read only",
		"check"	=> "validate_weak",
		"maxlen"=> 50,
		"crypt"	=> true,
		"msg"	=> ""),
	"author"	=> array(
		"label"	=> "Name - Admin",
		"check"	=> "validate_name",
		"maxlen"=> 50,
		"crypt"	=> false,
		"msg"	=> ""),
	"email"		=> array(
		"label"	=> "Email - Admin",
		"check"	=> "validate_email",
		"maxlen"=> 100,
		"msg"	=> ""),
	"challenge"	=> array(
		"label"	=> "Challenge Prompt",
		"check"	=> "validate_length",
		"maxlen"=> 100,
		"crypt"	=> false,
		"msg"	=> ""),
	"response"	=> array(
		"label"	=> "Challenge Response",
		"check"	=> "validate_length",
		"maxlen"=> 100,
		"crypt"	=> false,
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
	if (count(preg_split('/:/',$q)) < 4) {
		$msg = cell(warn(
		"Must have at least 3 non-lower case characters"));
		return 0;
	}
	return 1;
}


//------------ active code --------------

function get_form() {

	if (isset($_POST["clear"])) {
		// clear the posted values
		foreach ($this->list as $k => $v) {
			// clear all but username if update
			if ($this->type != "Update" || $k != "username") {
				$_POST[$k] = "";
				$$k = "";
			}
		}
	}
	// clean-up values
	foreach ($this->list as $k => $v) {
		if ($_POST[$k]) {
			$$k = $this->dequote(
				strip_tags(trim($_POST[$k])));
			$_POST[$k] = $$k;
		}
	}

	// validate the input fields
	$this->invalid = 0;
	foreach ($this->list as $k => $v) {
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

function show_form($title, $helpkey) {

	$thelp = sendhelp($title,$helpkey);
	card_head($title);
	print <<<EOT
<table class="tight">
<tr><th><center><big> $thelp </big></center>
</th></tr> <tr><th>
	<form action="{$_SERVER['PHP_SELF']}" method="POST">
	<table class="form">
EOT;

foreach ($this->list as $k => $v) {
	$label = sendhelp($this->list[$k]["label"],$k);
	$msg = $this->list[$k]["msg"];
	$ml = $this->list[$k]["maxlen"];

	if ($this->type=="Update" && $k == "username") {
		print row(cell($label,"class=\"h_form\"")."\n	".
		cell(input("hidden",$k,$_POST[$k]).$_POST[$k])
			.$msg)."\n";
	} else {
		print row(cell($label,"class=\"h_form\"")."\n	".
		cell(input("text",$k,$_POST[$k],"size=25 maxlength=$ml"))
			.$msg)."\n";
	}
}
print <<<EOT
	<tr><th colspan=3>
EOT;
	print	input("submit","submit",$this->type)
		.input("reset","reset","Reset")
		.input("submit","clear","Clear")
		.input("hidden","create_update_user","Done");
print <<<EOT
	</td></tr>
	</table>
	</form>
</th></tr>
</table>
EOT;
// need to add end stuff

	if ($this->invalid) {
		print warn(
"There were {$this->invalid} field(s) that need to be corrected")."<br/>\n";
	}
}

}

?>
