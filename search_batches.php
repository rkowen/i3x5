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

	$debug = 0;

	if (isset($_GET["errmsg"])) {
		$errmsg = $_GET["errmsg"];
	}
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<br/>\n"; exit; }

if (!isset($view)) {
	$_SESSION['view'] = new View();
	$view =& $_SESSION['view'];
}

function isOK($x = "") {
	$ret = FALSE;
	if (strlen($x) && array_key_exists($x,$_POST)) {
		$type = gettype($_POST[$x]);
		if ($type == "string" || $type == "integer") {
			$ret = TRUE;
			if ($type == "string") {
				if (! strlen(str_replace(' ','',$_POST[$x]))) {
					$ret = FALSE;
				}
			}
		}
	}
	return $ret;
}

// process batches if given view go-ahead
if (isset($_POST["simple"]) && ($_POST["simple"] == "Search")) {
	$view->get_buttons("1");
	if (isOK("keyword")) {
		if (isset($_POST["case"])) {
			$case = TRUE;
		} else {
			$case = FALSE;
		}
		$view->cards = $db->cards_simple($user->uid,
			$_POST["keyword"], $case);
		$view->lastcardsql($db->lastcardsql());
		session_write_close();
		header("Location: view_cards.php");
	} else {
		$errmsg = "Must give a string to search for.";
	}
	return;
} else if (isset($_POST["advanced"]) && ($_POST["advanced"] == "Search")) {
	$sqli = "";
	$sqln = "";
	$sqlt = "";
	$sqlc = "";
	$sqlcd = "";
	$sqlmd = "";
	$sqlcm = "";
	$sql = "";
	$errmsg = "";
	$view->get_buttons("2");
// parse through statements
//	CID
	if (isset($_POST["comp0"]) && (strpos($_POST["comp0"],"NULL") > 0)) {
		$sqli .= "( id ".$_POST["comp0"].") ";
		goto cidend;
	}
	if (isOK("cid2")) {
		// BETWEEN
		if (!isOK("cid1")) {
			$errmsg .=
		"Card ID: must give a range of numbers for BETWEEN.  ";
			goto cidend;
		}
		$sqli .= "(id BETWEEN ";
		if ($_POST["cid1"] < $_POST["cid2"]) {
			$sqli .= $_POST["cid1"]." AND ".$_POST["cid2"].") ";
		} else {
			$sqli .= $_POST["cid2"]." AND ".$_POST["cid1"].") ";
		}
		goto cidend;
	}
	if (isOK("cid1")) {
		// search
		$sqli .= "( id ".$_POST["comp1"].$_POST["cid1"].") ";
	}
cidend:
	if (strlen($sqli)) {
		if (strpos($_POST["andor0"],"AND") !== FALSE) {
			$sqla .= $_POST["andor0"].$sqli;
		} else {
			$sqlo .= $_POST["andor0"].$sqli;
		}
	}
//	Number
	if (isset($_POST["comp1"]) && (strpos($_POST["comp1"],"NULL") > 0)) {
		$sqln .= "( num ".$_POST["comp1"].") ";
		goto numend;
	}
	if (isOK("num2")) {
		// BETWEEN
		if (!isOK("num1")) {
			$errmsg .=
		"Number: must give a range of numbers for BETWEEN.  ";
			goto numend;
		}
		$sqln .= "(num BETWEEN ";
		if ($_POST["num1"] < $_POST["num2"]) {
			$sqln .= $_POST["num1"]." AND ".$_POST["num2"].") ";
		} else {
			$sqln .= $_POST["num2"]." AND ".$_POST["num1"].") ";
		}
		goto numend;
	}
	if (isOK("num1")) {
		// search
		$sqln .= "( num ".$_POST["comp1"].$_POST["num1"].") ";
	}
numend:
	if (strlen($sqln)) {
		if (strpos($_POST["andor1"],"AND") !== FALSE) {
			$sqla .= $_POST["andor1"].$sqln;
		} else {
			$sqlo .= $_POST["andor1"].$sqln;
		}
	}
//	Title
	if (isset($_POST["comp2"]) && (strpos($_POST["comp2"],"NULL") > 0)) {
		$sqlt .= "( title ".$_POST["comp2"].") ";
		goto titleend;
	}
	if (isOK("title2")) {
		// BETWEEN
		if (!isOK("title1")) {
			$errmsg .=
		"Title: must give a range of values for BETWEEN.  ";
			goto titleend;
		}
		$sqlt .= "(title BETWEEN ";
		if ($_POST["title1"] < $_POST["title2"]) {
		$sqlt .= "'".pg_escape_string($_POST["title1"])."' AND '"
			.pg_escape_string($_POST["title2"])."') ";
		} else {
		$sqlt .= "'".pg_escape_string($_POST["title2"])."' AND '"
			.pg_escape_string($_POST["title1"])."') ";
		}
		goto titleend;
	}
	if (isOK("title1")) {
		// search
		$sqlt .= "( title ".$_POST["comp2"]."'"
			.pg_escape_string($_POST["title1"])."') ";
	}
titleend:
	if (strlen($sqlt)) {
		if (strpos($_POST["andor2"],"AND") !== FALSE) {
			$sqla .= $_POST["andor2"].$sqlt;
		} else {
			$sqlo .= $_POST["andor2"].$sqlt;
		}
	}
// Card
	if (isset($_POST["comp3"]) && (strpos($_POST["comp3"],"NULL") > 0)) {
		$sqlc .=
($db->encode && $user->encode
?		"(( card ".$_POST["comp3"]." AND encrypted IS FALSE)"
		." OR (xcard ".$_POST["comp3"]." AND encrypted IS TRUE)) "
:		"( card ".$_POST["comp3"]." AND encrypted IS FALSE) ");
		goto cardend;
	}
	if (isOK("card1")) {
		// search
		$sqlc .=
($db->encode && $user->encode
?		"(( card ".$_POST["comp3"]
			." '".pg_escape_string($_POST["card1"])."'"
			." AND n.encrypted IS FALSE)"
		." OR (pgp_safe_decrypt(xcard,'{$user->crypt}') "
		.$_POST["comp3"]." '".pg_escape_string($_POST["card1"])."'"
			." AND n.encrypted IS TRUE)) "
:		"( card ".$_POST["comp3"]."'"
			.pg_escape_string($_POST["card1"])."') ");
	}
cardend:
	if (strlen($sqlc)) {
		if (strpos($_POST["andor3"],"AND") !== FALSE) {
			$sqla .= $_POST["andor3"].$sqlc;
		} else {
			$sqlo .= $_POST["andor3"].$sqlc;
		}
	}
// Create Date
// YYYY-MM-DDTHH:MM
	$cd1 = "TO_TIMESTAMP('".pg_escape_string(
		preg_replace("/T/"," ",$_POST["createdate1"]))
		."','YYYY-MM-DD HH24:MI')";
	$cd2 = "TO_TIMESTAMP('".pg_escape_string(
		preg_replace("/T/"," ",$_POST["createdate2"]))
		."','YYYY-MM-DD HH24:MI')";
	if (isOK("createdate2")) {
		// BETWEEN
		if (!isOK("createdate1")) {
			$errmsg .=
		"Creation Date: must give a range of values for BETWEEN.  ";
			goto createdateend;
		}
		$sqlcd .= "(n.createdate BETWEEN ";
		if ($cd1 < $cd2) {
			$sqlcd .= $cd1." AND ".$cd2.") ";
		} else {
			$sqlcd .= $cd2." AND ".$cd1.") ";
		}
		goto createdateend;
	}
	if (isOK("createdate1")) {
		// search
		$sqlcd .= "(n.createdate ".$_POST["comp4"]." ".$cd1.")";
	}
createdateend:
	if (strlen($sqlcd)) {
		if (strpos($_POST["andor4"],"AND") !== FALSE) {
			$sqla .= $_POST["andor4"].$sqlcd;
		} else {
			$sqlo .= $_POST["andor4"].$sqlcd;
		}
	}
// Mod Date
// YYYY-MM-DDTHH:MM
	$md1 = "TO_TIMESTAMP('".pg_escape_string(
		preg_replace("/T/"," ",$_POST["moddate1"]))
		."','YYYY-MM-DD HH24:MI')";
	$md2 = "TO_TIMESTAMP('".pg_escape_string(
		preg_replace("/T/"," ",$_POST["moddate2"]))
		."','YYYY-MM-DD HH24:MI')";
	if (isOK("moddate2")) {
		// BETWEEN
		if (!isOK("moddate1")) {
			$errmsg .=
		"Modification Date: must give a range of values for BETWEEN.  ";
			goto moddateend;
		}
		$sqlmd .= "(n.moddate BETWEEN ";
		if ($md1 < $md2) {
			$sqlmd .= $md1." AND ".$md2.") ";
		} else {
			$sqlmd .= $md2." AND ".$md1.") ";
		}
		goto moddateend;
	}
	if (isOK("moddate1")) {
		// search
		$sqlmd .= "(n.moddate ".$_POST["comp5"]." ".$md1.")";
	}
moddateend:
	if (strlen($sqlmd)) {
		if (strpos($_POST["andor5"],"AND") !== FALSE) {
			$sqla .= $_POST["andor5"].$sqlmd;
		} else {
			$sqlo .= $_POST["andor5"].$sqlmd;
		}
	}
// compare createdate and moddate
	if ($_POST["comp6"] != " ") {
		$sqlcm .= "(n.createdate ".$_POST["comp6"]." n.moddate)";
		if (strpos($_POST["andor6"],"AND") !== FALSE) {
			$sqla .= $_POST["andor6"].$sqlcm;
		} else {
			$sqlo .= $_POST["andor6"].$sqlcm;
		}
	}
// collect
	// remove first AND
	$sqla = preg_replace("/^AND /","",$sqla,1);
	if (!strlen($sqla) && !strlen($sqlo)) {
		$errmsg = "The advanced search needs some conditionals given.";
	}
	if (!strlen($sqla) && strlen($sqlo)) {
		// just given an OR statement
		$sqla = $sqlo;
		$sqlo = "";
		$sqla = preg_replace("/^OR /","",$sqla,1);
	}
	$sql = "(".$sqla.") ".$sqlo;
if (!$debug) {
	if (!strlen($errmsg)) {
		$view->cards = $db->cards_advance($user->uid,$sql);
		$view->lastcardsql($db->lastcardsql());
		session_write_close();
		header("Location: view_cards.php");
	}
}
} else if (isset($_GET["cidbatch"])) {	/* show all related cards */
	$cid = $_GET["cidbatch"];
	if (!preg_match('/^d+$/', $cid)) {
		$errmsg = "Invalid string for card id - don't do that again!";
	}
	$view->cards = $db->related_cards($cid);
	$view->lastcardsql($db->lastcardsql());
	session_write_close();
	header("Location: view_cards.php?view_body=header");
}

function sqlcomp($n = "",$t = "string") {
	if ($t == "string") {
		return "<!--{ $n -->"
.select("comp$n",
  option("= ","IS")
 .option("!= ","IS NOT")
 .option("~ ","~")
 .option("!~ ","!~")
 .option("~* ","~*")
 .option("!~* ","!~*")
 .option("LIKE ","LIKE")
 .option("NOT LIKE ","NOT LIKE")
 .option("ILIKE ","ILIKE")
 .option("NOT ILIKE ","NOT ILIKE")
 .option("SIMILAR TO ","SIMILAR TO")
 .option("NOT SIMILAR TO ","NOT SIMILAR TO")
 .option("IS NULL ","IS NULL")
 .option("IS NOT NULL ","IS NOT NULL"),
"style=\"font-size:60%;\"")
."<!--} $n -->";
	} elseif ($t == "number") {
		return "<!--{ $n -->"
.select("comp$n",
  option("= ","=")
 .option("!= ","!=")
 .option("< ","<")
 .option("<= ","<=")
 .option("> ",">")
 .option(">= ",">=")
 .option("IS NULL ","IS NULL")
 .option("IS NOT NULL ","IS NOT NULL"),
"style=\"font-size:60%;\"")
."<!--} $n -->";
	} elseif ($t == "date") {
		return "<!--{ $n -->"
.select("comp$n",
  option("= ","=")
 .option("!= ","!=")
 .option("< ","<")
 .option("<= ","<=")
 .option("> ",">")
 .option(">= ",">="),
"style=\"font-size:60%;\"")
."<!--} $n -->";
	} elseif ($t == "datex") {
		return "<!--{ $n -->"
.select("comp$n",
  option(" "," ")
 .option("= ","=")
 .option("!= ","!=")
 .option("< ","<")
 .option("<= ","<="),
"style=\"font-size:60%;\"")
."<!--} $n -->";
	}
}

function sqlandor($n = "") {
	return "<!--{ $n -->"
.select("andor$n",
  option("AND ","AND")
 .option("AND NOT ","AND NOT")
 .option("OR ","OR")
 .option("OR NOT ","OR NOT"),
"style=\"font-size:60%;\"")
."<!--} $n -->";
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
row(cell(label("keyword",sendhelp("Keyword","keyword search")),
	"style=\"text-align:right;\"")
.cell(input("text","keyword","")))
.row(cell(label("case","Case Sensitive"),"style=\"text-align:right;\"")
.cell(input("checkbox","case","case"),"style=\"text-align:left;\""))
.row(head($view->string_buttons("1"),"colspan=2"))
.row(head(
	input("submit","simple","Search")
	.input("reset","reset","Clear"),"colspan=2"))
		,"class=\"tight\"")."<!--}-->\n")
.h(3,"Advanced")
   .div(
	"<!--{-->".table(
row(head(sendhelp("Advanced Search","advanced search")
	."<br/><hr/>","colspan=\"4\""))
.row(head(sendhelp("AND/OR","sql and or")).cell(" ")
	.head(sendhelp("Comparison","sql comp")).cell(" "))
.row(cell(sqlandor("0"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(label("cid",sendhelp("Card ID","cid")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(sqlcomp("0","number")).cell(input("number","cid1","")))
.row(cell(sendhelp("BETWEEN","sql between"))
	.cell(input("number","cid2","")))
.row(cell(sqlandor("1"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(label("number",sendhelp("Number","number")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(sqlcomp("1","number")).cell(input("number","num1","")))
.row(cell(sendhelp("BETWEEN","sql between"))
	.cell(input("number","num2","")))
.row(cell(sqlandor("2"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(label("title",sendhelp("Title","title")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(sqlcomp("2")).cell(input("text","title1","")))
.row(cell(sendhelp("BETWEEN","sql between"))
	.cell(input("text","title2","")))
.row(cell(sqlandor("3"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"1\"")
.cell(label("card",sendhelp("Card","card")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"1\"")
.cell(sqlcomp("3")).cell(input("text","card1","")))
.row(cell(sqlandor("4"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(label("createdate",sendhelp("Creation Date","createdate")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(sqlcomp("4","date")).cell(input("datetime-local","createdate1","",
	"style=\"font-size:60%;\"")))

.row(cell(sendhelp("BETWEEN","sql between"))
	.cell(input("datetime-local","createdate2","",
	"style=\"font-size:60%;\"")))
.row(cell(sqlandor("5"),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(label("moddate",sendhelp("Modification Date","moddate")),
	"style=\"text-align:center; vertical-align: top;\" rowspan=\"2\"")
.cell(sqlcomp("5","date")).cell(input("datetime-local","moddate1","",
	"style=\"font-size:60%;\"")))
.row(cell(sendhelp("BETWEEN","sql between"))
	.cell(input("datetime-local","moddate2","",
	"style=\"font-size:60%;\"")))
.row(cell(sqlandor("6"),
	"style=\"text-align:center; vertical-align: top;\"")
.cell(label("createdatex",sendhelp("Creation Date","createdate")),
	"style=\"text-align:center; vertical-align: top;\"")
.cell(sqlcomp("6","datex"))
.cell(label("moddatex",sendhelp("Modification Date","moddate")),
	"style=\"text-align:center; vertical-align: top;\""))
.row(head($view->string_buttons("2"),"colspan=\"4\""))
.row(head(
	input("submit","advanced","Search")
	.input("reset","reset","Clear"),"colspan=\"4\""))
,"class=\"tight\"")."<!--}-->\n")
,"id=\"smenu\""))."\n";
	showphpinfo();
	print <<<PAGE
<script type="text/javascript">
// .ready
$(function() {
        $("#smenu").accordion({
                collapsible:    true,
                active:         0,
		heightStyle:	"content"
        });
});
</script>
PAGE;
if ($debug) {
print("<br/><bold>results</bold><br/>\n");
print("<br/><bold>$sql</bold><br/>\n");
print("<br/><bold>$errmsg</bold><br/>\n");
print("<pre style=\"text-align:left;\">view=\n");
print_r($_POST);
print("</pre>\n");
}
	card_foot();
?>
