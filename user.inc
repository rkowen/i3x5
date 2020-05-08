<?php
//
// DESC: user class for managing uid and batch mappings
//
class User {
	var $uid;
	var $uname;
	var $level;
	var $bids;	// hash array of hash arrays
			// (bid1=>("bid"=> ,"batch"=> ,"selected"=> ), ...)

// constructor
	function User($uid, $uname, $level, $project, &$bids) {
		$this->uid   = $uid;
		$this->uname = $uname;
		$this->level = $level;
		$this->project = $project;
		$this->bids = $bids;
	}

// rest the session values
	function session () {
		if (isset($_SESSION["user"])) {
			$_SESSION["user"] = $user;
		}
	}
// reset array of bids
	function update_bids (&$bids) {
		reset($bids);
		while (list($k,$v) = each($bids)) {
			if ($this->bids[$k]) {
			  $bids[$k]["selected"] = $this->bids[$k]["selected"];
			}
		}
		unset($this->bids);
		$this->bids = $bids;
	}
// provide count of selected arrays
	function selected_count() {
		$count = 0;
		if (is_array($this->bids) ) {
			reset($this->bids);
			while (list($k,$v) = each($this->bids)) {
				  if ($this->bids[$k]["selected"]) {$count++;}
			}
		}
		return $count;
	}
}
?>
