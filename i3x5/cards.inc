<?php
// DESC: common values, functions, and classes
//
// values
//
	$schema		= "i3x5";
	$version	= "0.7.0";
	$phpinfo	= 0;	// set non-zero for phpinfo
	$result_bg	= "BGCOLOR=\"#EFEFEF\"";
	$title_bg	= "BGCOLOR=\"#AFFFAF\"";
	$help_bg	= "BGCOLOR=\"#AFAFFF\"";
	$box_color	= "#F0D0D0";
	$form_color	= "#D0D0D0";
	$warn_color	= "#DD0000";
	$inform_color	= "#0000DD";
	$relate_color	= "#8080DD";
	$related_color	= "#CC5050";
	$noselect_color	= "#D0D0D0";
	$head_color	= "#F0F0F0";
	$help_color	= "#805000";
	$indent		= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
	function warn($q) {
		global $warn_color;
		return "<FONT COLOR=\"$warn_color\">$q</FONT>";
	}
	function inform($q) {
		global $inform_color;
		return "<FONT COLOR=\"$inform_color\">$q</FONT>";
	}
	function help($q) {
		global $help_color;
		return "<FONT COLOR=\"$help_color\">$q</FONT>";
	}
	function table($q, $op="") {
		return "<TABLE $op>\n$q\n</TABLE>\n";
	}
	function head($q, $op="") {
		return "<TH $op>$q</TH>\n";
	}
	function row($q, $op="") {
		return "<TR $op>$q</TR>\n";
	}
	function cell($q, $op="") {
		return "<TD $op>$q</TD>\n";
	}
	function pre($q, $op="") {
		if ($op == "t") {
			return "<PRE>$q</PRE>";
		} else {
			return "$q";
		}
	}
	function sendhelp($text, $key = "unknown") {
		global $help_color;
		return "<A HREF=\"indexB.php?help=".urlencode($key).
			"\" TARGET=\"options\">".
			"<FONT COLOR=\"$help_color\">$text</FONT></A>\n";
	}
	function senddesc($text, $bid, $property) {
		global $help_color;
		return "<A HREF=\"indexB.php?bid=$bid&property=$property".
			"\" TARGET=\"options\">".
			"<FONT COLOR=\"$help_color\">$text</FONT></A>\n";
	}
?>
