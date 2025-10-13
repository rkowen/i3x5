<?php
//
// DESC: class object with common values to be used and shared.
//

//
// values
//
	$schema		= "i3x5";
	$version	= "0.9.5";
	$phpinfo	= 0;	// set non-zero for phpinfo
	$debug		= 0;	// html debug
	$debugdb	= 0;	// DB debug
	$level_root	= 99;
	$level_admin	= 4;
	$level_write	= 3;
	$level_append	= 2;
	$level_read	= 1;
	$level_names	= array (
		$level_root	=> "Root",
		$level_admin	=> "Admin",
		$level_write	=> "Write",
		$level_append	=> "Append-Only",
		$level_read	=> "Read-Only",
		0		=> "Invalid");

class Common {
	public	$schema = "i3x5";	// database schema
	public	$encode = 0;		// whether to use encryption or not
	public	$no_new = 0;		// whether to allow new users
	public	$debug = 0;		// global debug value
	public	$showphpinfo = 0;	// show phpinfo

// constructor
	function __construct() {
		global $common;
		global $phpinfo;
		if (isset($_SESSION["common"])) {
			$common = $_SESSION["common"];
		}
		$this->showphpinfo = $phpinfo;
	}
	function show() {
		print <<<SHOW
<pre>
schema		= $this->schema
encode		= $this->encode
no_new		= $this->no_new
debug		= $this->debug
showphpinfo	= $this->showphpinfo
</pre></br>
SHOW;
	}
}
?>
