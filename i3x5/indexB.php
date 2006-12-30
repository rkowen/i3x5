<?php
// DESC: options frame, shows access level and selection options,
// DESC: displays the help information when clicked upon elsewhere
	include_once "user.inc";
	session_start();
	include_once "session.inc";
	include_once "cards.inc";
	if (isset($_GET["bid"])) {
		$bid = $_GET["bid"];
	}
	if (isset($_GET["property"])) {
		$property = $_GET["property"];
	}
	if (isset($_GET["help"])) {
		$help = $_GET["help"];
	}
	card_head("Button Frame","options",0);
if (isset($user)) {
	print "<H4>".sendhelp($level_names[$user->level],
		$level_names[$user->level])
		." Access Menus</H4>\n";
//	print "access level = $access_level\n";

	print "<ul>\n";

	if ($user->level >= $level_admin) {
		print <<< EOT
<li> View/Update:
  <ul>
  <li> <A HREF="update_user.php" TARGET="main">User Info</A><BR>
  </ul>
EOT;
	}
	print "<li> Batch Properties\n  <ul>\n";

	if ($user->level >= $level_write) {
		print <<< EOT
  <li> <a href="new_batch.php?batch_select=New&example=card" target="main">Create</a>
   or <a href="batches.php" target="main">Update</a> Batch
EOT;
	}
	if ($user->level >= $level_admin) {
		print <<< EOT
  <li> <a href="del_batch.php" target="main">Delete Batch</a>
EOT;
	}
	print <<< EOT
  <li> <a href="list_batches.php" target="main">List Batches</a>
  </ul>
EOT;
	$csvhelp = sendhelp("(help)", "csv file");
	print <<< EOT
<li> <a href="sel_batches.php" target="main">Select Batches</a>
  <ul>
  <li> <a href="view_cards.php?view_edit=list" target="main">View</a>
EOT;
	if ($user->level >= $level_append) {
		print "&nbsp;or&nbsp;";
		print <<< EOT
  <a href="view_cards.php?view_edit=edit" target="main">Edit</a>
EOT;
	}
	print <<< EOT
	 Batches
  <li> <a href="csv_cards.php" target="main">CSV file</a> $csvhelp
  </ul>
</ul>
EOT;

}
	print "\n<HR>\n";

	include_once "3x5_db.inc";
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	if (isset($help)) {
		print "<H4>Help</H4>\n";
		print "<P class=\"help\">".help($db->helpmsg($help))."\n";
	} elseif (isset($bid)) {
		print "<H4>Project Help</H4>\n";
		print "<P class=\"help\">"
			.help($db->helpdesc($bid,$property))."\n";
	}
	
	card_foot(0);
?>
