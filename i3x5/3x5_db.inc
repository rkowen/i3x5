<?php
//////////////////////////////////////////////////////////////////////////
//
// DESC: 3x5 card DB connection class
// DESC: handles most 3x5 DB interactions with explicitly formatted queries
//
//////////////////////////////////////////////////////////////////////////
include_once "rko_db.inc";

class i3x5_DB extends RKO_DB {
//////////////////////////////////////////////////////////////////////////
// attributes
//////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////
// constructor
//////////////////////////////////////////////////////////////////////////
function i3x5_DB ($schema = "3x5") {
	// $this->debug = 0;
	$this->errno = 0;
	$this->errmsg = "i3x5_DB: ";
	$this->schema = $schema;
	if ($this->debug) {
		echo "3x5_DB constructor($this)<BR>\n";
	}
	$this->dbconn = pg_Connect($this->host,$this->user,$this->passwd,
		$this->schema);
	if (!$this->dbconn) {
		$this->errmsg.="Can't connect as ".
			"($this->host,$this->user,$this->passwd,$this->schema)";
		$this->errno=-1;
		return false;
	}
}
//////////////////////////////////////////////////////////////////////////
// methods
//////////////////////////////////////////////////////////////////////////

//
// return bids array (suitable for $user->bid)
function &bids ($uid) {
	if ($this->debug > 0) {
		echo "3x5_DB::bids($this,$uid)<BR>\n";
	}
	return $this->multi_col("bid",
"(SELECT bid, batch, rid, 0 AS selected,\n".
"num_name AS num, title_name AS title, card_name AS card,\n".
"num_help, title_help, card_help,\n".
"moddate AS date\n".
"FROM i3x5_batch WHERE uid=$uid AND rid IS NULL\n".
"UNION\n".
"SELECT n.bid, n.batch, n.rid, 0 AS selected,\n".
"r.num_name AS num, r.title_name AS title, r.card_name AS card,\n".
"r.num_help, r.title_help, r.card_help,\n".
"r.moddate AS date\n".
"FROM i3x5_batch n, i3x5_batch r\n".
"WHERE n.uid=$uid AND r.bid=n.rid AND n.bid IS NOT NULL\n".
") ORDER BY batch");
}

//
// return the field names for a given bid
function &batch_fieldnames($bid) {
	if ($this->debug > 0) {
		echo "3x5_DB::batch_fieldnames($this,$bid)<BR>\n";
	}
	// keep recursing until find one which is not "related"
	while ($bid) {
		$one_row_data = $this->one_row(
"SELECT bid,rid,batch,\n".
"num_name AS num, title_name AS title ,card_name AS card, ".
"num_help, title_help, card_help, ".
"moddate AS date ".
"FROM i3x5_batch ".
"WHERE bid=$bid");
		$bid = $one_row_data["rid"];
	};
	return $one_row_data;
}

//
// return a hash array of cards for the given $bids
// set $selected to false to get all cards for the user
function &cards (&$bids, $selected = true) {
	if ($this->debug > 0) {
		echo "3x5_DB::cards($this,$bids,$selected)<BR>\n";
	}

	if ($selected) {
		$list = "";
		reset($bids);
		while (list($k,$v) = each($bids)) {
			if ($v[selected]) { $list .= "$k,"; }
		}
		$list = preg_replace("/,$/","",$list);
	} else {
		$list = join(",",array_keys($bids));
	}
	return $this->multi_col("id",
"SELECT id,bid,rid,num,title,card,\n".
" (SELECT count(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,\n".
" moddate AS date\n".
"FROM i3x5_cards n WHERE bid in ($list) AND rid IS NULL\n".
"UNION\n".
"SELECT n.id AS id, n.bid AS bid, n.rid AS rid,\n".
"r.num AS num,r.title AS title,\n".
"r.card AS card,0 AS nrid, r.moddate AS date\n".
"FROM i3x5_cards n, i3x5_cards r\n".
"WHERE n.bid in ($list) AND r.id=n.rid AND n.rid IS NOT NULL\n");
}

// insert a card, return the id
// $object = array(num=>, title=>, card=> )  or rid
// returns false if bad input
function insert_card ($bid, $object, $append = false) {
	if ($this->debug > 0) {
		echo "3x5_DB::insert_card($this,$object)<BR>\n";
	}
	if (is_array($object)) {	// array of things
		if (! ($object[num] || $object[title] || $object[card])) {
			return false;
		}
		if (! $object[num]) { $object[num] = "NULL"; }
		if (! $object[title]) { $object[title] = "NULL"; }
		else { $object[title] = "'".$object[title]."'";}
		if (! $object[card]) { $object[card] = "NULL"; }
		elseif ($append) {
			$object[card] = 
"'<<<'||to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD HH24:MI:SS')||'>>>\n".
			$object[card]."' "; }
		else { $object[card] = "'".$object[card]."'";}

		$id = $this->sql("SELECT nextval('i3x5_cards_id_seq')");
		$this->sql(
"INSERT INTO i3x5_cards (id,bid,num,title,card) ".
"VALUES ($id, $bid, {$object[num]}, {$object[title]}, {$object[card]})");
		return $id;
	} elseif(is_int($object)) {
		$id = $this->sql("SELECT nextval('i3x5_cards_id_seq')");
		$this->sql(
"INSERT INTO i3x5_cards (id,bid,rid) VALUES ($id, $bid, $object)");
		return $id;
	}
	return false;
}

// update a card, return the id
// $object = array(num=>, title=>, card=> )
// returns false if bad input
function update_card ($id, $rid, $object) {
	if ($this->debug > 0) {
		echo "3x5_DB::update_card($this,$object)<BR>\n";
	}
	if (! ($object[num] || $object[title] || $object[card])) {
		return false;
	}
	// update to related card if given
	if ($rid) {$id = $rid;}
	// prepare query
	$q = "";
	if ($object[num]) { $q.="num={$object[num]},"; }
	if ($object[title]) { $q.="title='".$object[title]."',";}
	if ($object[card]) { $q.="card='".$object[card]."',";}
	// strip final ,
	$q = preg_replace("/,$/","",$q);
	
	return $this->sql("UPDATE i3x5_cards SET $q WHERE id=$id");
}

// append to a card, given the card id,rid,card_append
// returns false if bad input else id or card appended to
function append_card ($id, $rid, $card_append) {
	if ($this->debug > 0) {
		echo "3x5_DB::append_card($this,$rid,$card_append)<BR>\n";
	}
	if (! ($id || $card_append)) {
		return false;
	}
	// do nothing if NULL input
	if (! $card_append) { return false; }

	// append to related card if given
	if ($rid) {$id = $rid;}
	// get previous card value
	$oldcard = $this->sql("SELECT card FROM i3x5_cards WHERE id=$id");
	// strip off trailing newline
	$oldcard = $this->quote(preg_replace("/\n$/","",$oldcard));
	$this->sql(
"UPDATE i3x5_cards SET card='$oldcard\n<<<'".
"||to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD HH24:MI:SS')||'>>>\n$card_append' ".
"WHERE id=$id");
	return $id;
}

// delete a card and all related entries
function delete_card ($id) {
	return $this->sql(
"DELETE FROM  i3x5_cards WHERE id=$id OR rid=$id");
}

// move a card to another batch
function move_card($id,$newbid) {
	return $this->sql(
"UPDATE i3x5_cards SET bid=$newbid WHERE id=$id");
}

// copy a card to another batch
function copy_card($id,$newbid) {
	return $this->sql(
"INSERT INTO i3x5_cards (bid,num,title,card)\n".
"SELECT $newbid,num,title,card FROM i3x5_cards WHERE id=$id");
}

function helpmsg($key = "unknown") {
	$t = $this->sql(
"SELECT help FROM i3x5_help WHERE key='$key'");
	return ($t ? $t : "$key : ".$this->helpmsg()) ;
}

function helpdesc($bid,$property) {
	if ($property == "batch") {
		$t = $this->sql(
"SELECT batch_help FROM i3x5_batch WHERE bid=$bid");
	} else {
		$t = $this->sql(
"SELECT {$property}_help FROM i3x5_batch WHERE bid=$bid AND rid IS NULL\n".
"UNION\n".
"SELECT r.{$property}_help FROM i3x5_batch n, i3x5_batch r\n".
"WHERE n.bid=$bid AND n.rid=r.bid AND n.rid IS NOT NULL"
);
	}
	return ($t ? $t : "No Description");
}

}// end of 3x5_DB
?>
