<?php
	include_once "user.inc";

	session_start();

	include_once "cards.inc";
print <<<PAGE
<HTML>
<BODY $help_bg>

PAGE;
if ($user) {
	print "<H4>".sendhelp($level_names[$user->level],
		$level_names[$user->level])
		." Access Menus</H4>\n";
//	print "access level = $access_level\n";

	if ($user->level >= $level_admin) {
		print <<< EOT
View/Update:<BR>
<A HREF="update_user.php" TARGET="main">User Info</A><BR>
EOT;
	}
	if ($user->level >= $level_write) {
	print <<< EOT
<A HREF="batches.php" TARGET="main">Batch Properties</A><BR>
$indent<A HREF="new_batch.php?example=card" TARGET="main">New Batch</A><BR>
EOT;
	} else {
	print <<< EOT
Batch Properties<BR>
EOT;
	}
	if ($user->level >= $level_admin) {
		print <<< EOT
$indent<A HREF="del_batch.php" TARGET="main">Delete Batch</A><BR>
EOT;
	}
	print <<< EOT
$indent<A HREF="list_batches.php" TARGET="main">List Batches</A><BR>
EOT;
	$csvhelp = sendhelp("(help)", "csv file");
	print <<< EOT
<A HREF="sel_batches.php" TARGET="main">Select Batches</A><BR>
$indent<A HREF="csv_cards.php" TARGET="main">CSV file</A> $csvhelp<BR>
EOT;

}
	print "\n<HR>\n";

	include_once "3x5_db.inc";
	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }

	if ($help) {
		print "<H4>Help</H4>\n";
		print "<P ALIGN=left>".help($db->helpmsg($help))."\n";
	} elseif ($bid) {
		print "<H4>Project Help</H4>\n";
		print "<P ALIGN=left>"
			.help($db->helpdesc($bid,$property))."\n";
	}
	
print <<<PAGE
</BODY>
</HTML>

PAGE;
?>

