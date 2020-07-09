<?php
//
// DESC: user class for managing uid and batch mappings
//
class User {
	public $uid;
	public $uname;
	public $level;
	public $reallevel;
	public $bids;	// hash array of hash arrays
			// (bid1=>("bid"=> ,"batch"=> ,"selected"=> ), ...)

// constructor
	function __construct($uid, $uname, $level, $project, &$bids) {
		$this->uid   = $uid;
		$this->uname = $uname;
		$this->level = $level;
		$this->reallevel = $level;
		$this->project = $project;
		$this->bids = $bids;
	}

// set the session values
	function session () {
		global $user;
		if (isset($_SESSION["user"])) {
			$_SESSION["user"] = $user;
		}
	}
// reset array of bids
	function update_bids (&$bids) {
		reset($bids);
		while (list($k,$v) = each($bids)) {
			if (isset($this->bids[$k])
			and $this->bids[$k]) {
			  $bids[$k]["selected"] = $this->bids[$k]["selected"];
			}
		}
		unset($this->bids);
		$this->bids = $bids;
	}
// provide count of selected arrays
	function selected_count() {
		$count = 0;
		if (isset($this->bids) && is_array($this->bids) ) {
			reset($this->bids);
			while (list($k,$v) = each($this->bids)) {
				  if ($this->bids[$k]["selected"]) {$count++;}
			}
		}
		return $count;
	}
}
?>
