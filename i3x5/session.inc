<?php
// make certain session variables "global"
// (use references for convenience so changes will be propagated back)
//	This allows the register_globals directive to be off
//
// Registered variables:
//	user
//	view
//
	if (isset($_SESSION["user"])) {
		$user =& $_SESSION["user"];
	}
	if (isset($_SESSION["view"])) {
		$view =& $_SESSION["view"];
	}
?>
