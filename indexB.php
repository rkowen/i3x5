<?php
// DESC: options frame, shows access level and selection options
	include_once "user.inc";
	include_once "common.inc";
	include_once "session.inc";
	include_once "cards.inc";
	global $common;
if (isset($user)) {
	print sendhelp($level_names[$user->level],
		$level_names[$user->level])
		." Menus\n";
	//print "access level = $access_level<br/>\n";

	print "<ul>\n";
	print "<li> Batch Properties\n  <ul>\n";

	if ($user->level >= $level_write) {
		print <<< EOT
  <li> <a href="new_batch.php?batch_select=New&example=card" target="main">Create</a>
   or <a href="batches.php" target="main">Update</a></li>
EOT;
	}
	if ($user->level >= $level_admin) {
		print <<< EOT
  <li> <a href="del_batch.php" target="main">Delete</a></li>
EOT;
		if ($common->encode && $user->encode) {
			print <<< EOT
  <li> <a href="crypt_batch.php" target="main">Encrypt</a></li>
EOT;
		}
		print <<< EOT
  </ul>
EOT;
	}
	print <<< EOT
  <li>Batches:
  <ul>
  <li> <a href="list_batches.php" target="main">List</a></li>
EOT;
	$csvhelp = sendhelp("(help)", "csv file");
	print <<< EOT
  <li> <a href="search_batches.php" target="main">Search</a>
  <li> <a href="sel_batches.php" target="main">Select</a>
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
    </ul>
  </ul>
</ul>
EOT;
/*
  <li> <a href="xcards.php?view_edit=list" target="main">XView</a>
	 Batches</li>
  <li> <a href="csv_cards.php" target="main">CSV file</a> $csvhelp</li>
  </ul></li>
*/

}
?>
