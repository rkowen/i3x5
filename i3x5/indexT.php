<?php 
// DESC: title frame, shows username
	include_once "user.inc";
	include_once "cards.inc";

	session_start();
	include_once "session.inc";

print <<<PAGE
<html>
<head>
<link rel="stylesheet" type="text/css" href="3x5.css">
</head>
<body class="title">

PAGE;
if (isset($user)) {
	print "<B>".$user->project."</B><BR>\n"
		."username: <B>{$user->uname}</B><BR>\n"
		."access: <B>{$level_names[$user->level]}</B>\n";
}

print "<ul>\n";
if (!isset($user)) {
print <<<PAGE
<li><a href="login_user.php?login=Login+User" target="main">
	Login New User</a>
<li><a href="create_user.php" target="main">Create New User</a>
PAGE;
} else {
print <<<PAGE
<li><a href="logout_user.php" target="main">Logout User</a>
<li><a href="login_user.php?access" target="main">Change Access</a>
PAGE;
}
print <<<PAGE
</ul>
</body>
</html>
PAGE;
?>
