<?php
// DESC: sets up areas and iframes title/options/helptext/main
	include_once "cards.inc";
	include_once "user.inc";
	include_once "common.inc";
	include_once "3x5_db.inc";
	session_start();
	include_once "session.inc";

// get common for session
	$common = new Common();
// get DB connection for session
	$db = new i3x5_DB($common->schema);

// relate certain attributes
	$common->encode = $db->encode;
	$common->schema = $db->schema;

	if (isset($_SESSION["user"])) {
		$what = "indexM.php";
	} else {
		$what = "login_user.php";
	}
	print "<!DOCTYPE html>\n";
	print "<html>\n";
	script_head();
	print <<<PAGE
<head>
<title>3x5 Cards Project</title>
</head>
<body id="body">
<div id="page">
  <div id="bodyleft">
    <div id="menu">
<h3>User Project</h3>
      <div id="title" class="title">
PAGE;
	include_once "indexT.php";
	print <<<PAGE
      </div>
<h3>Access Menus</h3>
      <div id="options" class="options">
PAGE;
	include_once "indexB.php";
	print <<<PAGE
      </div>
    </div>
	<iframe name="helptext" id="helptext" width="100%" height="100%">Load Failed</iframe>
  </div>
  <div id="bodyright">
    $closeeye
	<iframe name="main" id="main" width="100%" height="100%" src="$what"></iframe>
</div>
</div>
<script type="text/javascript">
// .ready
$(function() {
	var url = window.location.href;
	var refer = document.referrer;

	FrameSize();
	$(window).resize(function() { FrameSize(); });
	if (url != refer) {
		window.open(url,"_top");
	}
	$("#menu").accordion({
		collapsible:	true,
		active:		false,
		heightStyle:	"fill"
	});
});
</script>
</body>
</html>
PAGE;
?>
