<?php
// DESC: select which batch or set of batches for card handling
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	session_register("view");
	include_once "session.inc";
	include_once "cards.inc";
	include_once "many_batch.inc";
	include_once "3x5_db.inc";

	if (isset($_GET["errmsg"])) {
		$errmsg = $_GET["errmsg"];
	}
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	$manybid = new ManyBatch();
	$manybid->get_many_batch();
	
if (isset($view)) {
	$view->get_buttons();
} else {
	$_SESSION['view'] = new View();
	$view =& $_SESSION['view'];
}
// process batches if given view go-ahead
if (isset($_POST["submit"]) && ($_POST["submit"] == "Submit")) {
	if ( (! is_array($_POST["many_batch__"]))
	|| (count($_POST["many_batch__"]) == 0)) {
		$errmsg = "Nothing to view! Select some batches.";
	} else {
		$manybid->set_user_selected();
#		header("Location: view_cards.php");
		print <<<PAGE
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css">
<title>{$user->project} - Selected Batches</title>
</head>
<body class="main">
<center>
<h2>
Pick a ``Select Batches'' operation from left menu<BR>
</h2>
</center>
PAGE;
	if ($phpinfo) {phpinfo(); $view->dump();}
		print "</body>\n";
		return;
	}
}

$hhead = sendhelp("{$user->project} - Select Batches", "batch select");
	print <<<PAGE
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css">
<title>{$user->project} - Select Batches</title>
</head>
<body class="main">
<center>
PAGE;
	print form($_SERVER['PHP_SELF'],
	"<!--{-->".table(row(head($hhead))
		.(isset($errmsg)?row(head(warn($errmsg)))."\n":"")
		.row(head("<!--{-->".table(row(head(
			$manybid->string_many_batch(),"valign=\"top\""))."\n"
			.row(head($view->string_buttons()))."\n"
			.row(head(
				input("submit","submit","Submit")
				.input("submit","submit","Check")
				.input("reset","reset","Reset")
				.input("submit","clear","Clear"))))))
	,"class=\"tight\""))."\n";
	if ($phpinfo) {phpinfo();}
	print <<<PAGE
</center>
</body>
</html>
PAGE;

?>
