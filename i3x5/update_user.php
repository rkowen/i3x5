<?php
	include_once "user.inc";

	session_start();

	include_once "cards.inc";
	include_once "3x5_db.inc";

	$db = new i3x5_DB($schema);
	if (! $db ) { print "initial:".$db->errmsg()."<BR>\n"; exit; }
	// $db->debug(1);

// get userpass data from DB if not done!
	if (! ereg("Done", $_POST["create_update_user"])) {
		if (! $db->query(
"SELECT * FROM i3x5_userpass WHERE uid={$user->uid}")){
			echo $db->errmsg();
		}
		if (! $db->exec()) { echo $db->errmsg(); }
		$data = $db->fetch();
		if (! $data ) { echo "No Data for uid={$user->uid}\n"; }
		// set values
		reset($data);
		while (list($k,$v) = each($data)) {
			$_POST[$k] = $db->dequote($v);
		}
	}

	// this needs to be set at global level to communicate down
	$type = "Update";
	include_once "create_update_user.inc";
	$cuu = new Create_Update_User("Update");

	$cuu->get_form();

	if (ereg("Done", $_POST["create_update_user"])) {
		// create query
		$query = "UPDATE i3x5_userpass SET ";
		reset ($cuu->list);
		while (list($k,$v) = each($cuu->list)) {
			if ($k != "username") {
				$query .= "$k='".
					$db->quote($_POST[$k])."',";
			}
		}
		// strip off last ,
// it looks like valid input ... now ship it off to the DB
		$query = preg_replace("/,$/","",$query);
		$query .= " WHERE username='".$_POST["username"]."'";
		$result = $db->sql($query);
		// print "query = $query<BR>\n";
		// print "result = $result<BR>\n";

		$cuu->show_form($user->project." - Update User", "update user");

		if ($result) {
			print <<<EOT
</CENTER>
<P>
Your '{$this->project}' has not been updated properly!<BR>
result = $result<BR>
</BODY>
</HTML>
EOT;
		}
			print <<<EOT
</CENTER>
<P>
Your '{$this->project}' user profile has been updated.<BR>
Click on a menu item to the left to do something else.
</BODY>
</HTML>
EOT;
	} else {

		$cuu->show_form($user->project." - Update User", "update user");
		print <<<EOT

</CENTER>
<P>
Your '{$this->project}' user profile has been updated.<BR>
Click on a menu item to the left to do something else.
</BODY>
</HTML>
EOT;
	}
?>
