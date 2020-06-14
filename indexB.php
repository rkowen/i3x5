<?php
// DESC: options frame, shows access level and selection options
	include_once "user.inc";
	include_once "session.inc";
	include_once "cards.inc";
if (isset($user)) {
	print sendhelp($level_names[$user->level],
		$level_names[$user->level])
		." Menus\n";
	//print "access level = $access_level<br/>\n";

	print "<ul>\n";

	if ($user->level >= $level_admin) {
		print <<< EOT
<li> View/Update:
  <ul>
  <li> <A HREF="update_user.php" TARGET="main">User Info</A><br/>
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
  <li> <a href="xcards.php?view_edit=list" target="main">XView</a>
	 Batches
  <li> <a href="csv_cards.php" target="main">CSV file</a> $csvhelp
  </ul>
</ul>
EOT;

}
?>
