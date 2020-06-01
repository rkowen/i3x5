<?php
// DESC: sets up iframes title/options/main
	session_start();

	include_once "cards.inc";

	if (isset($_SESSION["user"])) {
/*
		if (isset($_GET["_parent"])) {
			header("Window-target: _parent");
		}
*/
		$what = "indexM.php";
	} else {
		$what = "login_user.php";
	}
	print <<<PAGE
<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<head>
<title>3x5 Cards Project</title>
<link rel="stylesheet" type="text/css" href="3x5.css">
</head>
<body>
<div id="page">
<div id="bodyleft">
	<iframe name="title" id="title" src="indexT.php"></iframe>
<br/>
	<iframe name="options" id="options" src="indexB.php"></iframe>
<br/>
	<iframe name="helptext" id="helptext"></iframe>
</div>
<div id="bodyright">
	<iframe name="main" id="main" src="$what"></iframe>
</div>
</div>
<script>

function FrameSize() {
	var wid = $("body").width();
	var hit = $("body").height();
/* set some iframe widths (else default 150x300 */
	$("#page").height(hit);
	$("#bodyleft").height(hit).width(Math.min(250,wid*.25));
	$("#bodyright").height(hit).width(wid - $("#bodyleft").width());
	$("#main").width($("#bodyright").width()).height(hit);
	$("#title").width($("#bodyleft").width()).height(150);
	$("#options").width($("#bodyleft").width()).height(hit - 150 - 150);
	$("#helptext").width($("#bodyleft").width()).height(150);
}

$(document).ready(function() {
	var url = window.location.href;
	var refer = document.referrer;

	FrameSize();
	$(window).resize(function() { FrameSize(); });
	if (url != refer) {
		window.open(url,"_top");
	}
});
</script>
</body>
</html>
PAGE;
?>
