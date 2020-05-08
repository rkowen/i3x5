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
	function xcard_head ($title="", $class="main", $center=1, $extra="") {
		header("Content-type: text/xml");

$x ='<?xml-stylesheet type="text/xsl" href="3x5.xsl"?>'."\n";
		print <<<HEAD
<?xml version="1.0" encoding="utf-8" ?>
$x
<!-- 
- 3x5 XML Dump
-->
<i3x5>
HEAD;
	}
	function xcard_foot ($center=1) {
		PRINT <<<END
</i3x5>
END;
	}
	function card_head ($title="", $extra="") {
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
	function xmlshowphpinfo ($opt = INFO_VARIABLES) {
		global $phpinfo;
		ob_start();
		if ($phpinfo) {phpinfo($opt);}
		phpinfo($opt);
		$pinfo = ob_get_contents();
		ob_end_clean();
		//$fn=dirname($_SERVER["SCRIPT_FILENAME"])."/phpinfo.html";
		$fn="/tmp/phpinfo.html";
		$fb=fopen($fn,"wb");
		fwrite($fb,$pinfo);
		fclose($fb);
	}
	function showphpinfo ($opt = INFO_VARIABLES) {
		global $phpinfo;
		if ($phpinfo) {phpinfo($opt);}
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
		return "<a class=\"help\" href=\"indexB.php"
			."?help=".urlencode($key)
			."\" target=\"options\">"
			."$text</a>\n";
	}
	function senddesc($text, $bid, $property) {
		return "<a class=\"help\" href=\"indexB.php"
			."?bid=$bid&property=$property"
			."\" target=\"options\">"
			."$text</a>\n";
	}
	function xmlspecialchars($text) {
		return str_replace('&#039;', '&apos;',
			htmlspecialchars($text, ENT_QUOTES));
	}
?>
