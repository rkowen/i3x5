<?php
//////////////////////////////////////////////////////////////////////////
//
// DESC: 3x5 card DB connection class
// DESC: handles most 3x5 DB interactions with explicitly formatted queries
//
//////////////////////////////////////////////////////////////////////////
include_once "rko_db.inc";
include_once "cards.inc";

class i3x5_DB extends RKO_DB {
//////////////////////////////////////////////////////////////////////////
// constants
//////////////////////////////////////////////////////////////////////////
	protected $selectcards = "SELECT nn.*,b.batch FROM (
 SELECT id,bid,rid,num,title,card,
 (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
 createdate,TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
 moddate,TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
 formatted
FROM i3x5_cards n
WHERE n.rid IS NULL wherexxx
UNION
SELECT n.id AS id, n.bid AS bid, n.rid AS rid,
r.num AS num,r.title AS title,
r.card AS card,0 AS nrid,
r.createdate,TO_CHAR(r.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
r.moddate,TO_CHAR(r.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
r.formatted AS formatted
FROM	i3x5_cards n
JOIN	i3x5_cards r
ON	r.id=n.rid
WHERE	n.rid IS NOT NULL wherexxx)	nn
JOIN	i3x5_batch					b
ON	(nn.bid = b.bid)
orderbyxxx";
	protected $selectbatch = "(SELECT bid, batch, rid, 0 AS selected,
num_name AS num, title_name AS title, card_name AS card,
num_help, title_help, card_help,
createdate,TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
moddate,TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate
FROM	i3x5_batch	n
WHERE	rid IS NULL wherexxx
UNION
SELECT n.bid, n.batch, n.rid, 0 AS selected,
r.num_name AS num, r.title_name AS title, r.card_name AS card,
r.num_help, r.title_help, r.card_help,
r.createdate,TO_CHAR(r.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
r.moddate,TO_CHAR(r.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate
FROM	i3x5_batch	n
JOIN	i3x5_batch	r
ON	r.bid=n.rid
WHERE	n.bid IS NOT NULL wherexxx
) orderbyxxx";

//////////////////////////////////////////////////////////////////////////
// attributes
//////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////
// constructor
//////////////////////////////////////////////////////////////////////////
function __construct ($schema = "3x5") {
	$this->debug = 0;
	$this->showerr = 1;
	$this->errno = 0;
	$this->errmsg = "i3x5_DB: ";
	$this->schema = $schema;
	if ($this->debug) {
		echo "3x5_DB constructor()<br/>\n";
		echo "dbconn = pg_Connect(".$this->connect.") <br/>\n";
	}
	parent::__construct($schema);
	$this->dbconn = pg_Connect($this->connect);
	if (!$this->dbconn) {
		$this->errmsg.="Can't connect as ".
			"(".$this->connect.")";
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
		echo "3x5_DB::bids($uid)<br/>\n";
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
		echo "3x5_DB::batch_fieldnames($bid)<br/>\n";
	}
	// keep recursing until find one which is not "related"
	while ($bid) {
		$one_row_data = $this->one_row(
"SELECT bid,rid,batch,\n".
"num_name AS num, title_name AS title ,card_name AS card, ".
"num_help, title_help, card_help, ".
"moddate AS date,".
"createdate,TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,".
"moddate,TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate ".
"FROM i3x5_batch ".
"WHERE bid=$bid");
		$bid = $one_row_data["rid"];
	};
	return $one_row_data;
}

//
// return the field names for a card with related cards
function &card_batches($uid, $id) {
	if ($this->debug > 0) {
		echo "3x5_DB::card_batches($uid, $id)<br/>\n";
	}
/* check input $id */
	if (!preg_match('/^\d+$/',$id))	return false;
	return $this->multi_col("id",
"SELECT	c.id, bid, b.batch, c.basecard
FROM	i3x5_batch			b
JOIN	(
	SELECT	id, bid, false AS basecard
	FROM	i3x5_cards
	WHERE	rid = (
		SELECT	rid
		FROM	i3x5_cards
		WHERE	id = $id
	)
	UNION
	SELECT	n.id AS id, n.bid, true AS basecard
	FROM	i3x5_cards		n
	JOIN	i3x5_cards		r
	ON	(r.rid = n.id)
	WHERE	r.id = $id
	UNION
	SELECT	id, bid, id=COALESCE(rid,id) AS basecard
	FROM	i3x5_cards
	WHERE	id = $id
	  OR	rid = $id
)					c
USING	(bid)
WHERE	uid = $uid
");
}

// --{--
// return a hash array for one card
function &card ($card_id) {
	if ($this->debug > 0) {
		echo "3x5_DB::card($card_id)<br/>\n";
	}
	// reject if $card_id is not a number
	if (! is_int($card_id)) {
		$this->errmsg.="card:($card_id) not integer";
		$this->errno=-1;
		return false;
	} 
	$query = str_replace("wherexxx","AND n.id=$card_id",$this->selectcards);
	$query = str_replace("orderbyxxx","",$query);
	return $this->multi_col("id",$query);
}

// return all related cards given the base card_id
function &related_cards ($card_id) {
	if ($this->debug > 0) {
		echo "3x5_DB::card_related($card_id)<br/>\n";
	}
	// reject if $card_id is not a number
	if (!preg_match('/^\d+$/',$card_id)) {
		$this->errmsg.="card_related:($card_id) not integer";
		$this->errno=-1;
		return NULL;
	}
	$query = str_replace("wherexxx",
		"AND (n.id=$card_id OR n.rid = $card_id)",$this->selectcards);
	$query = str_replace("orderbyxxx","ORDER BY nrid DESC, id ASC",$query);
	return $this->multi_col("id",$query);
}

//
// return XML for one card
//	$card_id = id, query multi_col, or card hash
function &xml_card($card_id, $indent = "") {
	if ($this->debug > 0) {
		echo "3x5_DB::xml_card($card_id,$indent)<br/>\n";
	}
	$buffer = $indent."<xcard>\n";
	if (is_array($card_id)) {
		$r = $card_id;
	} else {
		$r = $this->card($card_id);
	}
	if (isset($r["id"])) {
		$v = $r;
	} else {
		list($k,$v) = each($r);
	}
	ksort($v);
	while (list($kk,$vv) = each($v)) {
		$buffer.=$indent."  <".$kk.">".xmlspecialchars($vv)
		."</".$kk.">\n";
		if ($kk == "title") {
			$buffer.=$indent."  <".$kk."x>"
			.strtolower(trim(xmlspecialchars($vv)))
			."</".$kk."x>\n";
		}
	}
	$buffer .= $indent."</xcard>\n";
	return $buffer;
}

function &cards ($select,$orderby="") {
	if ($this->debug > 0) {
		echo "3x5_DB::cards($select)<br/>\n";
	}

	$query = str_replace("wherexxx",$select,$this->selectcards);
	$query = str_replace("orderbyxxx",$orderby,$query);
	return $this->multi_col("id",$query);
}

//
// return XML for a collection of cards
//	pass in a select or a query multi_col
function &xml_cards($select = "", $orderby = "", $indent = "") {
	if ($this->debug > 0) {
		echo "3x5_DB::xml_cards($select,$orderby,$indent)<br/>\n";
	}
	$buffer = $indent."<xcards>\n";
	if (is_array($select)) {
		$r = $select;
	} else {
		$r = $this->cards($select,$orderby);
	}
	$buffer .= $indent."  <count>".count($r)."</count>\n";
	while (list($k,$v) = each($r)) {
		$buffer.=$this->xml_card($v,$indent."  ");
	}
	$buffer .= $indent."</xcards>\n";
	return $buffer;
}
//
// return a hash array for one batch
function &batch ($batch_id) {
	if ($this->debug > 0) {
		echo "3x5_DB::batch($batch_id)<br/>\n";
	}
	// reject if $batch_id is not a number
	if (! is_int($batch_id)) {
		$this->errmsg.="batch:($batch_id) not integer";
		$this->errno=-1;
		return false;
	} 
	$query = str_replace("wherexxx","AND n.bid=$batch_id",
		$this->selectbatch);
	$query = str_replace("orderbyxxx","",$query);
	return $this->multi_col("bid",$query);
}
//
// return XML for one batch
//	$batch_id = id, query multi_col, or batch hash
function &xml_batch($batch_id, $indent = "",$card_batch = "") {
	if ($this->debug > 0) {
		echo "3x5_DB::xml_batch($batch_id,$indent,...)<br/>\n";
	}
	$buffer = $indent."<xbatch>\n";
	if (is_array($batch_id)) {
		$r = $batch_id;
	} else {
		$r = $this->batch($batch_id);
	}
	if (isset($r["bid"])) {
		$v = $r;
	} else {
		list($k,$v) = each($r);
	}
	ksort($v);
	while (list($kk,$vv) = each($v)) {
		$buffer.=$indent."  <".$kk.">".xmlspecialchars($vv)
		."</".$kk.">\n";
	}
	$buffer .= $card_batch;
	$buffer .= $indent."</xbatch>\n";
	return $buffer;
}
//
// return XML for one batch
//	$batch_id = bid, query multi_col, or batch hash
//	$orderby for the card batch
function &xml_card_batch($batch_id, $indent = "",$orderby = "") {
	if ($this->debug > 0) {
	  echo "3x5_DB::xml_card_batch($batch_id,$indent,$orderby)<br/>\n";
	}
	if (is_array($batch_id)) {
		$r = $batch_id;
		$bid = $batch_id;
	} else {
		$r = $this->batch($batch_id);
	}
	if (isset($r["bid"])) {
		$v = $r;
		$bid = (int) $r["bid"];
	} else {
		list($k,$v) = each($r);
		$bid = $k;
	}
	$buffer = $this->xml_batch($bid,$indent,
		$this->xml_cards("AND n.bid=$bid",$orderby,$indent."  "));
	return $buffer;
}
//
// return XML for multiple batches
//	$bids = hash from &bids()
//	set $selected to false to get all cards for the user
//	$orderby for the card batch
function &xml_card_batches(&$bids, $selected = true,
		$indent = "",$borderby = "", $orderby = "") {

	if ($this->debug > 0) {
		echo "3x5_DB::xml_card_batchs($batch_id,$selected,"
		."$indent,$borderby,$orderby)<br/>\n";
	}
	$buffer = "";
	$cnt = 0;
	reset($bids);
	while (list($k,$v) = each($bids)) {
		if ($selected && ! $v["selected"]) {
			continue;
		}
		$buffer .= $this->xml_card_batch($k,$indent."  ");
		$cnt++;
	}
	$buffer .= $indent."</xbatches>\n";
	return $indent."<xbatches>\n".$indent."  <count>$cnt</count>\n".$buffer;
}
// --}--

//
// return a hash array of cards for the given $bids
// set $selected to false to get all cards for the user
function &cards_ (&$bids, $selected = true) {
	if ($this->debug > 0) {
		echo "3x5_DB::cards_($bids,$selected)<br/>\n";
	}

	if ($selected) {
		$list = "";
		reset($bids);
		while (list($k,$v) = each($bids)) {
			if ($v["selected"]) { $list .= "$k,"; }
		}
		$list = preg_replace("/,$/","",$list);
	} else {
		$list = join(",",array_keys($bids));
	}
	$this->lastcardsql = 
"SELECT id,bid,rid,num,title,card,\n".
" (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,\n".
" TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,\n".
" TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,\n".
" formatted\n".
"FROM i3x5_cards n WHERE bid in ($list) AND rid IS NULL\n".
"UNION\n".
"SELECT n.id AS id, n.bid AS bid, n.rid AS rid,\n".
"r.num AS num,r.title AS title,\n".
"r.card AS card,0 AS nrid,\n".
"TO_CHAR(r.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,\n".
"TO_CHAR(r.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,\n".
"r.formatted AS formatted\n".
"FROM i3x5_cards n, i3x5_cards r\n".
"WHERE n.bid in ($list) AND r.id=n.rid AND n.rid IS NOT NULL\n";

	return $this->multi_col("id",$this->lastcardsql);
}

//
// return a hash array of cards from a simple search
// set $case to true for a case sensitive search
function &cards_simple ($uid, $keyword, $case = false) {
	if ($this->debug > 0) {
		echo "3x5_DB::cards_simple($keyword,$case)<br/>\n";
	}
	$safekey = pg_escape_string($keyword);
	if ($case) {
		$like = "~";
	} else {
		$like = "~*";
	}

	$this->lastcardsql = 
"SELECT id,bid,n.rid,num,title,card,
 (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
 TO_CHAR(n.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
 TO_CHAR(n.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
 formatted
FROM i3x5_cards n
JOIN i3x5_batch b
USING (bid)
WHERE uid = $uid
AND (title $like '$safekey' OR card $like '$safekey')
AND n.rid IS NULL";

	return $this->multi_col("id",$this->lastcardsql);
}

//
// return a hash array of cards from an advance search
// $sql needs to be sanitized before call.
// set $norel = false to allow related cards in search
function &cards_advance ($uid, $sql, $norel = true) {
	if ($this->debug > 0) {
		echo "3x5_DB::cards_advance($sql)<br/>\n";
	}
	$this->lastcardsql = 
"SELECT id,bid,n.rid,num,title,card,
 (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
 TO_CHAR(n.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
 TO_CHAR(n.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
 formatted
FROM i3x5_cards n
JOIN i3x5_batch b
USING (bid)
WHERE uid = $uid
AND ($sql)";
	if ($norel) $this->lastcardsql .= "\nAND n.rid IS NULL";

	return $this->multi_col("id",$this->lastcardsql);
}
//
// rerun the last cards SQL query to have updated infor
// and return a hash array of cards.
function &cards_rerun () {
	if ($this->debug > 0) {
		echo "3x5_DB::cards_rerun(".
		$this->lastcardsql.")<br/>\n";
	}
	if (isset($this->lastcardsql)) {
		return $this->multi_col("id",$this->lastcardsql);
	}
}
//
// read/set lastcardsql
function lastcardsql($sql = "") {
	if ($sql == "") {
		if (isset($this->lastcardsql)) {
			return $this->lastcardsql;
		}
	} else {
		$this->lastcardsql = $sql;
	}
}

// insert a card, return the id
// $object = array(num=>, title=>, card=>, formatted=> )  or rid
// returns false if bad input
function insert_card ($bid, $object, $append = false) {
	if ($this->debug > 0) {
		echo "3x5_DB::insert_card($bid,$object,$append)<br/>\n";
	}
	if (is_array($object)) {	// array of things
		if (! ($object["num"] || $object["title"] || $object["card"])) {
			return false;
		}
		if (! $object["num"]) { $object["num"] = "NULL"; }
		if (! $object["title"]) { $object["title"] = "NULL"; }
		else { $object["title"]
			= "'".$this->escape($object["title"])."'";}
		if (! $object["card"]) { $object["card"] = "NULL"; }
		elseif ($append) {
			$object["card"] = 
"'<<<'||to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD HH24:MI:SS')||'>>>\n".
			$this->escape($object["card"])."' "; }
		else { $object["card"]
			= "'".$this->escape($object["card"])."'";}
		if (! $object["formatted"])
			{ $object["formatted"] = "false"; }

		$id = $this->sql("SELECT nextval('i3x5_cards_id_seq')");
		$this->sql(
"INSERT INTO i3x5_cards (id,bid,num,title,card,formatted) ".
"VALUES ($id, $bid, {$object['num']}, {$object['title']}, {$object['card']},".
" {$object['formatted']})" );
		return $id;
	} elseif((is_numeric($object) ? intval($object)==$object : false)) {
		// is_int($object) doesn't work as advertized
		$id = $this->sql("SELECT nextval('i3x5_cards_id_seq')");
		$this->sql(
"INSERT INTO i3x5_cards (id,bid,rid) VALUES ($id, $bid, $object)");
		return $id;
	}
	return false;
}

// update a card, return the id
// $object = array(num=>, title=>, card=>, formatted=> )
// returns false if bad input
function update_card ($id, $rid, $object) {
	if ($this->debug > 0) {
		echo "3x5_DB::update_card($object)<br/>\n";
	}
	if (! ($object["num"] || $object["title"] || $object["card"]
	|| $object["formatted"])) {
		return false;
	}
	// update to related card if given
	if ($rid) {$id = $rid;}
	// prepare query
	$q = "";
	if ($object["num"]) { $q.="num={$object["num"]},"; }
	if ($object["title"]) { $q.="title='"
			.$this->escape($object["title"])."',";}
	if ($object["card"]) { $q.="card='"
			.$this->escape($object["card"])."',";}
	if ($object["formatted"]) { $q.="formatted=".$object["formatted"].",";}
	else { $q.="formatted=false,";}
	// strip final ,
	$q = preg_replace("/,$/","",$q);
	
	return $this->sql("UPDATE i3x5_cards SET $q WHERE id=$id");
}

// append to a card, given the card id,rid,card_append
// returns false if bad input else id or card appended to
function append_card ($id, $rid, $card_append, $card_formatted) {
	if ($this->debug > 0) {
	  echo "3x5_DB::append_card($rid,$card_append,$card_formatted)<br/>\n";
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
	$oldcard = $this->escape(preg_replace("/\n$/","",$oldcard));
	// set the formatting
	if (! $card_formatted) { $card_formatted = "false"; }
	$card_append = $this->escape($card_append);
	$this->sql(
"UPDATE i3x5_cards SET card='$oldcard\n<<<'".
"||to_char(CURRENT_TIMESTAMP,'YYYY/MM/DD HH24:MI:SS')||'>>>\n$card_append', ".
"formatted=$card_formatted ".
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
"INSERT INTO i3x5_cards (bid,num,title,card,formatted)\n".
"SELECT $newbid,num,title,card,formatted FROM i3x5_cards WHERE id=$id");
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
