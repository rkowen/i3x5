<?php
// DESC: common values, functions, and classes
//
// values
//
	$schema		= "i3x5";
	$version	= "0.7.1";
	$phpinfo	= 0;	// set non-zero for phpinfo
	$form_color	= "#D0D0D0";
	$level_admin	= 4;
	$level_write	= 3;
	$level_append	= 2;
	$level_read	= 1;
	$level_names	= array (
		$level_admin	=> "Administration",
		$level_write	=> "Write",
		$level_append	=> "Append-Only",
		$level_read	=> "Read-Only",
		0		=> "Invalid");

//
// functions
//
	function card_head ($title="", $class="main", $center=1, $extra="") {
		print <<<HEAD
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css">
HEAD;
		if ($title != "") {
			print <<<TITLE
<title>
$title
</title>
TITLE;
		}
		if ($extra != "") {
			print $extra."\n";
		}
		PRINT <<<BODY
</head>
<body class="$class">
BODY;
		if ($center == 1) {
			print "<center>\n";
		}
	}
	function card_foot ($center=1) {
		if ($center == 1) {
			print "</center>\n";
		}
		PRINT <<<END
</body>
</html>
END;
	}
	// INFO_ALL or INFO_VARIABLES
	function showphpinfo ($opt = INFO_VARIABLES) {
		global $phpinfo;
		if ($phpinfo) {phpinfo($opt);}
		// echo "<pre>".print_r($_SESSION)."</pre>\n";
	}
	function warn($q) {
		return "<em class=\"warn\">$q</em>";
	}
	function inform($q) {
		return "<em class=\"inform\">$q</em>";
	}
	function noselect($q) {
		return "<em class=\"noselect\">$q</em>";
	}
	function help($q) {
		return "<em class=\"help\">$q</em>";
	}
	function table($q, $op="") {
		return "<table $op>\n$q\n</table>\n";
	}
	function head($q, $op="") {
		return "<th $op>$q</th>\n";
	}
	function row($q, $op="") {
		return "<tr $op>$q</tr>\n";
	}
	function cell($q, $op="") {
		return "<td $op>$q</td>\n";
	}
	function form($action, $q, $op="") {
		return "<form action=\"$action\" method=\"POST\" $op>"
		."$q</form>\n";
	}
	function input($type,$name,$value,$op="") {
		return "<input type=\"$type\" name=\"$name\""
		." value = \"$value\" $op>";
	}
	function textarea($name,$value,$op="") {
		return "<textarea name=\"$name\" $op>".$value."</textarea>\n";
	}
	function pre($q, $op="") {
		if ($op == "t") {
			return "<pre>$q</pre>";
		} else {
			return "$q";
		}
	}
	function sendhelp($text, $key = "unknown") {
		return "<a class=\"help\" href=\"indexH.php"
			."?help=".urlencode($key)
			."\" target=\"helptext\">"
			."$text</a>\n";
	}
	function senddesc($text, $bid, $property) {
		return "<a class=\"help\" href=\"indexH.php"
			."?bid=$bid&property=$property"
			."\" target=\"helptext\">"
			."$text</a>\n";
	}
	function xmlspecialchars($text) {
		return str_replace('&#039;', '&apos;',
			htmlspecialchars($text, ENT_QUOTES));
	}
?>
