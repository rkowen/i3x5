<?php
// DESC: sets up areas and iframes title/options/helptext/main
	include_once "cards.inc";
	include_once "user.inc";
	session_start();

	if (isset($_SESSION["user"])) {
		$what = "indexM.php";
	} else {
		$what = "login_user.php";
	}
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
	<iframe name="helptext" id="helptext">Load Failed</iframe>
  </div>
  <div id="bodyright">
	<iframe name="main" id="main" src="$what"></iframe>
</div>
</div>
<script type="text/javascript">
function FrameSize() {
	var wid = window.innerWidth - 32;
	var hit = window.innerHeight;
/*
	var wid = $("body").width();
	var hit = $("body").height();
*/
/* set some iframe widths (else default 150x300 */
	$("#page").height(hit);
	$("#bodyleft").height(hit).width(Math.min(250,wid*.25));
	$("#menu").height(hit*.6).width($("#bodyleft").width());
	$("#bodyright").height(hit).width(wid - $("#bodyleft").width());
	$("#main").width($("#bodyright").width()).height(hit);
	$("#helptext").width($("#bodyleft").width()).height(hit*.4);
}

$(document).ready(function() {
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
