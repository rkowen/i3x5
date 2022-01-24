<?php
//////////////////////////////////////////////////////////////////////////
//
// DESC: 3x5 card DB connection class
// DESC: handles most 3x5 DB interactions with explicitly formatted queries
//
//////////////////////////////////////////////////////////////////////////
include_once "rko_db.inc";
include_once "cards.inc";
include_once "common.inc";

class i3x5_DB extends RKO_DB {
//////////////////////////////////////////////////////////////////////////
// constants
//////////////////////////////////////////////////////////////////////////
	protected $selectcards =
"SELECT	nn.*,b.batch
FROM (
	SELECT id,bid,rid,num,title,
	pgp_select(card,xcard,'usercryptxxx',encrypted) AS card,
	(SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
	createdate,TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
	moddate,TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
	formatted, encrypted
FROM i3x5_cards	n
WHERE n.rid IS NULL wherexxx
UNION
SELECT	n.id AS id, n.bid AS bid, n.rid AS rid,
	r.num AS num,r.title AS title,
	pgp_select(r.card,r.xcard,'usercryptxxx',r.encrypted) AS card,
	0 AS nrid,
	r.createdate,TO_CHAR(r.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
	r.moddate,TO_CHAR(r.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
	r.formatted AS formatted,
	r.encrypted AS encrypted
FROM	i3x5_cards n
JOIN	i3x5_cards r
ON	r.id=n.rid
WHERE	n.rid IS NOT NULL wherexxx
)	nn
JOIN	i3x5_batch					b
ON	(nn.bid = b.bid)
orderbyxxx";
	protected $selectbatch =
"(SELECT bid, batch, rid, 0 AS selected,
	num_name AS num, title_name AS title,
	card_name AS card,
	num_help, title_help, card_help,
	createdate,TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
	moddate,TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate
FROM	i3x5_batch	n
WHERE	rid IS NULL wherexxx
UNION
SELECT	n.bid, n.batch, n.rid, 0 AS selected,
	r.num_name AS num, r.title_name AS title,
	r.card_name AS card,
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
	global $debugdb;
	$this->debug = 0;
	if ($debugdb > 0) {
		$this->debug = $debugdb;
	}
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
		// let the parent be specific
		$this->errmsg.="--- Can't connect ---";
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
"(
SELECT	bid, batch, rid, 0 AS selected,
	num_name AS num, title_name AS title, card_name AS card,
	num_help, title_help, card_help,
	moddate AS date
FROM	i3x5_batch
WHERE	uid=$uid
AND	rid IS NULL
UNION
SELECT	n.bid, n.batch, n.rid, 0 AS selected,
	r.num_name AS num, r.title_name AS title, r.card_name AS card,
	r.num_help, r.title_help, r.card_help,
	r.moddate AS date
FROM	i3x5_batch	n,
	i3x5_batch	r
WHERE	n.uid=$uid
AND	r.bid=n.rid
AND	n.bid IS NOT NULL
) ORDER BY batch");
}

function &bids_num ($uid) {
	if ($this->debug > 0) {
		echo "3x5_DB::bids_num($uid)<br/>\n";
	}
	return $this->multi_col("bid",
"SELECT	-1 AS bid, COUNT(id) AS num, COUNT(rid) AS numrid,
	COUNT(NULLIF(encrypted,false)) AS numcryp
FROM	(
SELECT	n.id, n.rid, n.encrypted
FROM	i3x5_cards	n
JOIN	i3x5_batch	b
USING	(bid)
WHERE	b.uid = $uid
)	x
GROUP BY bid
UNION
SELECT	bid, COUNT(id) AS num, COUNT(rid) AS numrid,
	COUNT(NULLIF(encrypted,false)) AS numcryp
FROM	(
SELECT	bid, n.id, n.rid, n.encrypted
FROM	i3x5_cards	n
JOIN	i3x5_batch	b
USING	(bid)
WHERE	b.uid = $uid
)	x
GROUP BY bid
ORDER BY bid");
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
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::card($card_id)<br/>\n";
	}
	// reject if $card_id is not a number
	if (! is_int($card_id)) {
		$this->errmsg.="card:($card_id) not integer";
		$this->errno=-1;
		return false;
	} 
	$query = str_replace(
		array("wherexxx","usercryptxxx","orderbyxxx"),
		array("AND n.id=$card_id",$user->crypt,""),
		$this->selectcards);
	return $this->multi_col("id",$query);
}

// return all related cards given the base card_id
function &related_cards ($card_id) {
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::card_related($card_id)<br/>\n";
	}
	// reject if $card_id is not a number
	if (!preg_match('/^\d+$/',$card_id)) {
		$this->errmsg.="card_related:($card_id) not integer";
		$this->errno=-1;
		return NULL;
	}
	$query = str_replace(
		array("wherexxx","usercryptxxx","orderbyxxx"),
		array("AND (n.id=$card_id OR n.rid = $card_id)",
			"XXX".$user->crypt,"ORDER BY nrid DESC, id ASC"),
		$this->selectcards);
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
		foreach ($r as $k => $v) { break; }
	}
	ksort($v);
	foreach ($v as $kk => $vv) {
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
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::cards($select)<br/>\n";
	}

	$query = str_replace(
		array("wherexxx","usercryptxxx","orderbyxxx"),
		array($select,$user->crypt,$orderby),
		$this->selectcards);
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
	foreach ($r as $k => $v) {
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
// --}--

//
// return a hash array of cards for the given $bids
// set $selected to false to get all cards for the user
function &cards_ (&$bids, $selected = true) {
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::cards_($bids,$selected)<br/>\n";
	}

	if ($selected) {
		$list = "";
		reset($bids);
		foreach ($bids as $k => $v) {
			if ($v["selected"]) { $list .= "$k,"; }
		}
		$list = preg_replace("/,$/","",$list);
	} else {
		$list = join(",",array_keys($bids));
	}
	$this->lastcardsql = 
"SELECT	id,bid,rid,num,title,
	pgp_select(card,xcard,'{$user->crypt}',encrypted) AS card,
	(SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
	TO_CHAR(createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
	TO_CHAR(moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
	formatted, encrypted
FROM i3x5_cards n WHERE bid in ($list) AND rid IS NULL
UNION
SELECT	n.id AS id, n.bid AS bid, n.rid AS rid,
	r.num AS num,r.title AS title,
	pgp_select(r.card,r.xcard,'{$user->crypt}',r.encrypted) AS card,
	0 AS nrid,
	TO_CHAR(r.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
	TO_CHAR(r.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
	r.formatted AS formatted,
	r.encrypted AS encrypted
FROM i3x5_cards n, i3x5_cards r
WHERE n.bid in ($list) AND r.id=n.rid AND n.rid IS NOT NULL\n";

	return $this->multi_col("id",$this->lastcardsql);
}

//
// return a hash array of cards from a simple search
// set $case to true for a case sensitive search
function &cards_simple ($uid, $keyword, $case = false) {
	global $user;
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
"SELECT id,bid,n.rid,num,title,
 COALESCE(card,pgp_safe_decrypt(xcard,'{$user->crypt}')) AS card,
 (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
 TO_CHAR(n.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
 TO_CHAR(n.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
 n.formatted, n.encrypted
FROM i3x5_cards n
JOIN i3x5_batch b
USING (bid)
WHERE uid = $uid
AND (title $like '$safekey'
	OR (card $like '$safekey' AND n.encrypted IS FALSE)\n"
.($this->encode && $user->encode
?"	OR (pgp_safe_decrypt(xcard,'{$user->crypt}') $like '$safekey'
		AND n.encrypted IS TRUE)\n"
:	"")
.") AND n.rid IS NULL";

	return $this->multi_col("id",$this->lastcardsql);
}

//
// return a hash array of cards from an advance search
// $sql needs to be sanitized before call.
// set $norel = false to allow related cards in search
function &cards_advance ($uid, $sql, $norel = true) {
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::cards_advance($sql)<br/>\n";
	}
	$this->lastcardsql = 
"SELECT id,bid,n.rid,num,title,
 COALESCE(card,pgp_safe_decrypt(xcard,'{$user->crypt}')) AS card,
 (SELECT COUNT(rid) FROM i3x5_cards WHERE rid=n.id) AS nrid,
 TO_CHAR(n.createdate,'DD Mon YYYY HH24:MI:SS') AS cdate,
 TO_CHAR(n.moddate,'DD Mon YYYY HH24:MI:SS') AS mdate,
 formatted, n.encrypted
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
// $object = array(num=>, title=>, card=>, formatted=>, encrypted=> )  or rid
// returns false if bad input
function insert_card ($bid, $object, $cryptkey = "", $append = false) {
	if ($this->debug > 0) {
		echo "3x5_DB::insert_card($bid,$object,$append)<br/>\n";
	}
	if (is_array($object)) {	// array of things
		if (! ($object["num"] || $object["title"] ||$object["card"])) {
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
		if (! $object["encrypted"])
			{ $object["encrypted"] = "false"; }

		$id = $this->sql("SELECT nextval('i3x5_cards_id_seq')");
		$this->sql(
"INSERT INTO i3x5_cards (
	id,bid,num,title,
	card, xcard,
	formatted,encrypted
) VALUES (
	$id, $bid, {$object['num']}, {$object['title']},".
(($object["encrypted"] == "true") && strlen($cryptkey)
?	"NULL,pgp_sym_encrypt({$object["card"]},'$cryptkey'),"
:	"{$object["card"]},NULL,")
."	{$object['formatted']}, {$object['encrypted']})" );
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
// $object = array(num=>, title=>, card=>, formatted=>, encrypted=> )
// returns false if bad input
function update_card ($id, $rid, $object) {
	global $user;
	if ($this->debug > 0) {
		echo "3x5_DB::update_card($object)<br/>\n";
		echo "3x5_DB::encode={$this->encode})<br/>\n";
		echo "3x5_DB::user->encode={$user->encode})<br/>\n";
		echo "3x5_DB::encrypted={$object["encrypted"]})<br/>\n";
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
	if ($object["card"]) {
		// handle card and encrypted fields
		if (($this->encode>0) && ($user->encode>0)
		&& ($object["encrypted"] == "true")) {
			$q.="card=NULL,xcard=pgp_sym_encrypt('"
			.$this->escape($object["card"])
			."','{$user->crypt}'),encrypted=true,";
		} else {
			$q.="xcard=NULL,card='"
			.$this->escape($object["card"])."',encrypted=false,";}
		}
	if ($object["formatted"]) { $q.="formatted=".$object["formatted"].",";}
	else { $q.="formatted=false,";}
	// strip final ,
	$q = preg_replace("/,$/","",$q);
	
	return $this->sql("UPDATE i3x5_cards SET $q WHERE id=$id");
}

// append to a card, given the card id,rid,append
// returns false if bad input else id or card appended to
function append_card ($id,$rid,$append,$formatted,$encrypted) {
	global $user;
	if ($this->debug > 0) {
	  echo "3x5_DB::append_card($rid,$append,$formatted,$encrypted)<br/>\n";
	}
	if (! ($id || $append)) {
		return false;
	}
	// do nothing if NULL input
	if (! $append) { return false; }

	// append to related card if given
	if ($rid) {$id = $rid;}
	// get previous card value
	$oldcard = $this->sql(
"SELECT pgp_select(card,xcard,'{$user->crypt}',encrypted) AS card
FROM i3x5_cards WHERE id=$id");
	// strip off trailing newline
	$oldcard = $this->escape(preg_replace("/\n$/","",$oldcard));
	// set the formatting
	if (! $formatted) { $formatted = "false"; }
	$append = $this->escape($append);
	$x ="$oldcard\n"
		."<<<'||TO_CHAR(CURRENT_TIMESTAMP,'YYYY/MM/DD HH24:MI:SS')||"
		."'>>>\n$append";
	$this->sql(
"UPDATE i3x5_cards SET ".
($this->encode && $user->encode && ($encrypted == "true")
?	"xcard=pgp_sym_encrypt('$x','{$user->crypt}'),"
:	"card='$x',")
	."formatted=$formatted 
WHERE id=$id");
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
"INSERT INTO i3x5_cards (
	bid,num,title,card,xcard,formatted,encrypted
) SELECT $newbid,num,title,card,xcard,formatted,encrypted
FROM i3x5_cards
WHERE id=$id");
}

// Count the number of encrypted cards
function count_xcards($uid) {
	return $this->sql(
"SELECT	COUNT(1)
FROM	i3x5_cards	n
JOIN	i3x5_batch	b
USING	(bid)
WHERE	uid = $uid
AND	xcard IS NOT NULL
AND	n.encrypted");
}

// Convert the encrypted cards to another key
function convert_xcards($uid, $oldcrypt, $newcrypt) {
	$oldcrypt = $this->escape(clean($oldcrypt));
	$newcrypt = $this->escape(clean($newcrypt));
	if ((!strlen($oldcrypt)) || (!strlen($newcrypt))) {
		return NULL;
	}
	return $this->sql(
"UPDATE	i3x5_cards
SET	xcard = pgp_sym_encrypt(pgp_safe_decrypt(xcard,'$oldcrypt'),'$newcrypt')
WHERE	bid IN (
	SELECT	bid
	FROM	i3x5_batch
	WHERE	uid = $uid
)
AND	xcard IS NOT NULL
AND	encrypted");
}

// encrypt unencrypted cards in a batch
function encrypt_batch($bid, $crypt) {
	$crypt = $this->escape(clean($crypt));

	$this->sql("SELECT alter_cards_trigs(FALSE)");

	$retmsg = $this->sql(
"UPDATE	i3x5_cards
SET	xcard = pgp_sym_encrypt(card,'$crypt'),
	encrypted = TRUE,
	card = NULL
WHERE	bid = $bid
AND	card IS NOT NULL
AND	NOT encrypted");

	$this->sql("SELECT alter_cards_trigs(TRUE)");

	return $retmsg;
}

// decrypt encrypted cards in a batch
function decrypt_batch($bid, $crypt) {
	$crypt = $this->escape(clean($crypt));

	$this->sql("SELECT alter_cards_trigs(FALSE)");

	$retmsg = $this->sql(
"UPDATE	i3x5_cards
SET	card = pgp_safe_decrypt(xcard,'$crypt'),
	encrypted = FALSE,
	xcard = NULL
WHERE	bid = $bid
AND	xcard IS NOT NULL
AND	encrypted");

	$this->sql("SELECT alter_cards_trigs(TRUE)");

	return $retmsg;
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

function helphint($uid, $property = "Crypt") {
	$x = strtolower($property);
	$t = $this->sql(
"SELECT {$x}hint FROM i3x5_userpass WHERE uid=$uid");
	return ($t ? $t : "No Hint");
}

// search the 3x5 project help info
// $keywords = string of keywords
function helpsearch($keywords) {
	if ($this->debug > 0) {
		echo "3x5_DB::helpsearch($keywords)<br/>\n";
	}
	$keywords = $this->escape(clean($keywords));
	if (!strlen($keywords)) {
		return;
	}
	// strip out everything but characters and whitespace
	$keywords = preg_replace('/[^\s\p{L}\p{N}]/','',$keywords);
	$y = preg_split("/\s\s*/",$keywords);
	$z = "";
	foreach ($y as $k) {
		$z .= "AND	(key ~* '$k' OR help ~* '$k')\n";
	}
	$z = preg_replace("/^AND/","WHERE",$z);
	return $this->multi_col("key",
"SELECT	key, help
FROM	i3x5_help
$z");
}

}// end of 3x5_DB
?>
