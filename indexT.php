<?php 
// DESC: title frame, shows username
	include_once "user.inc";
	include_once "cards.inc";
	include_once "session.inc";

if (isset($user)) {
	print "<b>".$user->project."</b><br/>\n"
		."username: <b>{$user->uname}</b><br/>\n"
		."access: <b>{$level_names[$user->level]}</b>\n";
}

print "<ul>\n";
if (!isset($user)) {
print <<<PAGE
<li><a href="login_user.php?login=Login+User" target="main">
	Login New User</a></li>
<li><a href="create_user.php" target="main">Create New User</a></li>
PAGE;
} else {
	print <<<PAGE
<li><a href="logout_user.php" target="main">Logout User</a></li>
<li><a href="login_user.php?access" target="main">Change Access</a></li>
PAGE;
	if ($user->reallevel > $level_append) {
		print <<<PAGE
<li><a href="user_role.php" target="main">Change Role</a></li>
PAGE;
	}
	if ($user->level >= $level_admin) {
		print <<< EOT
<li> View/Update:
  <ul>
  <li> <a href="update_user.php" target="main">User Info</a><br/></li>
  </ul>
EOT;
	}
}
	print "</ul>\n";
?>
