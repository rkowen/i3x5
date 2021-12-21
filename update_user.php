<?php
	include_once "user.inc";

	session_start();
	include_once "session.inc";

	include_once "cards.inc";
	include_once "3x5_db.inc";
	global $user;

	$db = new i3x5_DB($schema);
	if (! $db ) {
		print "update_user initial:".$db->errmsg()."<br/>\n"; exit; }
	// $db->debug(1);

// get userpass data from DB if not done!
	if (! isset($_POST["create_update_user"])
	|| (! preg_match("/Done/", $_POST["create_update_user"]))) {
		if (! $db->query(
($db->encode ?
"SELECT username, project,
	pgp_sym_decrypt(xpasswd_admin,'{$db->crypt}') AS passwd_admin,
	pgp_sym_decrypt(xpasswd_w,'{$db->crypt}') AS passwd_w,
	pgp_sym_decrypt(xpasswd_a,'{$db->crypt}') AS passwd_a,
	pgp_sym_decrypt(xpasswd_r,'{$db->crypt}') AS passwd_r,
	author, email, challenge, response
FROM i3x5_userpass WHERE uid=".$user->uid
:
"SELECT username, project,
	passwd_admin,
	passwd_w,
	passwd_a,
	passwd_r,
	author, email, challenge, response
FROM i3x5_userpass WHERE uid=".$user->uid
))){
			echo "update_user error1: ".$db->errmsg();
		}
		if (! $db->exec()) {
			echo "update_user error2: ".$db->errmsg();
		}
		$data = $db->fetch();
		if (! $data ) {
			echo "No Data for uid=".$user->uid."\n";
			// echo "<pre>user = ".print_r($user)."</pre>\n";
		}
		// set values
		foreach ($data as $k => $v) {
			$_POST[$k] = $db->dequote($v);
		}
	}

	// this needs to be set at global level to communicate down
	$type = "Update";
	include_once "create_update_user.inc";
	$cuu = new Create_Update_User("Update");

	$cuu->get_form();

	if (isset($_POST["create_update_user"])
	&&  preg_match("/Done/", $_POST["create_update_user"])) {
		// create query
		$query = "UPDATE i3x5_userpass SET ";
		foreach ($cuu->list as $k => $v) {
			if ($k != "username") {
				if($v["crypt"]) {
($db->encode ?
					$query .= "x$k=".
	"pgp_sym_encrypt('".$db->quote($_POST[$k])."','{$db->crypt}'),"
:
					$query .= "$k=".
	"'".$db->quote($_POST[$k])."',"
);
				} else {
					$query .= "$k='".
						$db->quote($_POST[$k])."',";
				}
			}
		}
		// strip off last ,
// it looks like valid input ... now ship it off to the DB
		$query = preg_replace("/,$/","",$query);
		$query .= " WHERE username='".$_POST["username"]."'";
		$result = $db->sql($query);
		// print "query = $query<br/>\n";
		// print "result = $result<br/>\n";

		$cuu->show_form($user->project." - Update User Project",
			"update user");

		if (isset($result)) {
			print <<<EOT
</center>
<p>
Your '{$user->project}' has not been updated properly!<br/>
result = $result<br/>
</body>
</html>
EOT;
		}
			print <<<EOT
</center>
<p>
Your '{$user->project}' user profile has been updated.<br/>
Project changes will not be evident until next login.<br/>
Click on a menu item to the left to do something else.
</body>
</html>
EOT;
	} else {

		$cuu->show_form($user->project." - Update User Project",
			"update user");
		print <<<EOT

</center>
<p>
Click on a menu item to the left to do something else.
</body>
</html>
EOT;
	}
?>
