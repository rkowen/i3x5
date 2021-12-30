<?php
//
// DESC: class object with common values to be used and shared.
//
class Common {
	public	$schema = "i3x5";	// database schema
	public	$encode = 0;		// whether to use encryption or not
	public	$debug = 0;		// global debug value
	public	$showphpinfo = 0;	// show phpinfo

// constructor
	function __construct() {
		global $common;
		if (isset($_SESSION["common"])) {
			$common = $_SESSION["common"];
		}
	}
	function show() {
		print <<<SHOW
<pre>
schema		= $schema
encode		= $encode
debug		= $debug
showphpinfo	= $showphpinfo
</pre></br>
SHOW;
	}
}
?>
