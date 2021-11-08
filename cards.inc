<?php
// DESC: common values, functions, and classes
//
// values
//
	$schema		= "i3x5";
	$version	= "0.7.1";
	$phpinfo	= 0;	// set non-zero for phpinfo
	$form_color	= "#D0D0D0";
	$level_root	= 99;
	$level_admin	= 4;
	$level_write	= 3;
	$level_append	= 2;
	$level_read	= 1;
	$level_names	= array (
		$level_root	=> "Root",
		$level_admin	=> "Admin",
		$level_write	=> "Write",
		$level_append	=> "Append-Only",
		$level_read	=> "Read-Only",
		0		=> "Invalid");
	$xhidden	=
	"<span style=\"font-size: larger\">&blacktriangleright;</span>";
	$xshown		=
	"<span style=\"font-size: larger\">&blacktriangledown;</span>";
	$xshowmore	=
	"<span style=\"font-size: larger\">&diams;</span>";
	$xshowless	=
	"<span style=\"font-size: larger\">&blacksquare;</span>";
	$relate_color	=	"#a0a0dd";	// light blue
	$related_warn	=	"#cc5050";	// red

	global $user;

//
// functions
//
	function script_head() {
		print <<<SCRIPT
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="3x5.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="3x5.js"></script>
SCRIPT;
	}
	function card_head ($title="", $class="main", $center=1, $extra="") {
		global $_SESSION;
		global $_SERVER;
		global $_GET;
		global $user;
	// can't redirect if already in login_user.php or giving helpful info
		if ((!preg_match("/login_user.php/", $_SERVER["SCRIPT_NAME"])
		&& (!preg_match("/indexH.php/", $_SERVER["SCRIPT_NAME"]))
		&& (!isset($user, $user->uid)))) {
				header("Location: login_user.php");
		}

		print "<html>\n";
		script_head();
		print <<<HEAD
<head>
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
		if ($phpinfo) {
			phpinfo($opt);
			print "<pre>".print_r($_SESSION)."</pre><br/>\n";
		}
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
	function input($type, $name, $value, $op="") {
		return "<input type=\"$type\" name=\"$name\""
		." value = \"$value\" $op>";
	}
	function label($name, $q, $op="") {
		return "<label for=\"$name\" $op>"."$q</label>\n";
	}
	function textarea($name,$value,$op="") {
		return "<textarea name=\"$name\" $op>".$value."</textarea>\n";
	}
	function button($name, $q, $op="", $type="button") {
		return"<button name=\"$name\" type=\"$type\" $op>$q</button>\n";
	}

	function select($name,$q,$op="") {
		return "<select name=\"$name\" $op>".$q."</select>\n";
	}
	function option($value, $q, $op="") {
		return "<option value=\"$value\" $op>".$q."</option>\n";
	}

	function h($n, $q, $op="") {
		return "<h$n $op>$q</h$n>\n";
	}
	function ul($q, $op="") {
		return "<ul $op>$q</ul>\n";
	}
	function ol($q, $op="") {
		return "<ol $op>$q</ol>\n";
	}
	function li($q, $op="") {
		return "<li $op>$q</li>\n";
	}

	function span($q, $op="") {
		return "<span $op>$q</span>\n";
	}
	function div($q, $op="") {
		return "<div $op>$q</div>\n";
	}
	function ahref($href, $q, $op="") {
		return "<a href=\"$href\" $op>$q</a>\n";
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
	function toghide($cid,$full) {
		global $xshown, $xhidden;
		return
" <a id=\"tog_$cid\" class=\"nonprint togcard_but\" "
." href=\"javascript:hidecard('card_$cid','tog_$cid');\">"
.(($full == "full") ? $xshown : $xhidden )."</a>";
	}
?>
