<?php
// DESC: select which batch or set of batches for card handling
	include_once "user.inc";
	include_once "view.inc";
	session_start();
	include_once "session.inc";
	$_SESSION['view'] = $view;
//	session_register("view");
	include_once "cards.inc";
	include_once "many_batch.inc";
	include_once "3x5_db.inc";

	if (isset($_GET["errmsg"])) {
		$errmsg = $_GET["errmsg"];
	}
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

if (isset($view)) {
	$view->get_buttons();
} else {
	$_SESSION['view'] = new View();
	$view =& $_SESSION['view'];
}
// process batches if given view go-ahead
if (isset($_POST["simple"]) && ($_POST["simple"] == "Search")) {
	if (isset($_POST["keyword"]) && strlen($_POST["keyword"])) {
		if (isset($_POST["case"])) {
			$case = TRUE;
		} else {
			$case = FALSE;
		}
		$view->cards = $db->cards_simple($user->uid,
			$_POST["keyword"], $case);
		session_write_close();
		header("Location: view_cards.php?view_edit=list");
	} else {
		$errmsg = "Must give a string to search for.";
	}
	return;
}

$hhead = sendhelp("{$user->project} - Search Batches", "search select");
	card_head("{$user->project} - Search Batches");

	print form($_SERVER['PHP_SELF'],
$hhead
.(isset($errmsg)?"<br/>".warn($errmsg)."\n":"")
.div(
  h(3,"Simple")
   .div(
	"<!--{-->".table(
row( cell(label("keyword","Keyword"),"style=\"text-align:right;\"")
.cell(input("text","keyword","")))
.row( cell(label("case","Case Sensitive"),"style=\"text-align:right;\"")
.cell(input("checkbox","case","case"),"style=\"text-align:left;\""))
.row(head(
	input("submit","simple","Search")
	.input("reset","reset","Clear"),"colspan=2"))
,"class=\"tight\"")."<!--}-->\n")
  .h(3,"Advanced")
   .div(
	"<!--{-->".table(
		row(head("<!--{-->".table(
			row(head($view->string_buttons()))."\n"
			.row(head(
				input("submit","submit","Search")
				.input("reset","reset","Reset")
				.input("submit","clear","Clear"))))."<!--}-->"))
	,"class=\"tight\"")
   )
,"id=\"smenu\""))."\n";
	showphpinfo();
	print <<<PAGE
<script type="text/javascript">
$(document).ready(function() {
        $("#smenu").accordion({
                collapsible:    true,
                active:         0
        });
});
</script>
PAGE;
	card_foot();
?>
