<?php
// DESC: sets up frames title/options/main
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
<head>
<title>3x5 Cards Project</title>
<link rel="stylesheet" type="text/css" href="3x5.css">
</head>
<frameset border=2 cols="200, 100%" onLoad="if (self != top) top.location = self.location">
	<frameset border=2 rows="120, 100%">
		<frame name="title" src="indexT.php">
		<frame name="options" src="indexB.php">
	</frameset>
	<frame name="main" src="$what">
</frameset>
<noframe>
<body>
You need to have a Frames capable browser!
</body>
</noframe>
</html>
PAGE;
?>
