<?
//
// user class
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

// reset array of bids
	function update_bids (&$bids) {
		reset($bids);
		while (list($k,$v) = each($bids)) {
			if ($this->bids[$k]) {
			  $bids[$k]["selected"] = $this->bids[$k]["selected"];
			}
		}
		$this->bids = $bids;
	}
}
?>
