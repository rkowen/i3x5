<?php
// DESC: make certain session variables "global"
// DESC: (use references for convenience so changes will be propagated back)
// DESC: 	This allows the register_globals directive to be off
// DESC: Session registered variables:
// DESC: 	user
// DESC: 	view
// DESC: 	common
//
	global $user, $view, $common, $db;
	if (isset($_SESSION["user"])) {
		$user =& $_SESSION["user"];
	}
	if (isset($_SESSION["view"])) {
		$view =& $_SESSION["view"];
	}
	if (isset($_SESSION["common"])) {
		$common =& $_SESSION["common"];
	}
?>
