<?php
//////////////////////////////////////////////////////////////////////////
//
// DESC: RKO_DB simple DB connection class
// DESC: direct connection to DB with generalized operations
//
//////////////////////////////////////////////////////////////////////////
class RKO_DB {
//////////////////////////////////////////////////////////////////////////
// attributes
//////////////////////////////////////////////////////////////////////////
	var $host;
	var $user;
	var $passwd;
	var $schema;
	var $query;
	var $debug;
	var $errno;
	var $errmsg;			// append errmsgs only
	var $dbconn;
	var $dbresult;
	var $dbfetch;
	var $dbrnum;			// number of rows
	var $dbrow;			// current row
	var $dbfields;			// array of fields (to accept)
	var $dbfnum;			// number of fields
	var $dbfset;			// was dbfields changed?

//////////////////////////////////////////////////////////////////////////
// constructor
//////////////////////////////////////////////////////////////////////////
function RKO_DB ($schema) {
	$this->debug = 0;
	$this->errno = 0;
	$this->errmsg = "RKO_DB: ";
	$this->schema = $schema;
	if ($this->debug) {
		echo "RKO_DB constructor($this)<BR>\n";
	}
	$this->dbconn = pg_Connect($host,$user,$passwd,$schema);
	if (!$this->dbconn) {
		$this->errmsg.="Can't connect as ($host,$user,$passwd,$schema)";
		$this->errno=-1;
		return false;
	}
}

//////////////////////////////////////////////////////////////////////////
// object dump
//////////////////////////////////////////////////////////////////////////
function dump () {
	echo "<BR>=========== RKO_DB ====<BR>\n";
	echo "host	= ".$this->host."<BR>\n";
	echo "user	= ".$this->user."<BR>\n";
	echo "passwd	= ".$this->passwd."<BR>\n";
	echo "schema	= ".$this->schema."<BR>\n";
	echo "query	= ".$this->query."<BR>\n";
	echo "debug	= ".$this->debug."<BR>\n";
	echo "errno	= ".$this->errno."<BR>\n";
	echo "errmsg	= ".$this->errmsg."<BR>\n";
	echo "dbconn	= ".$this->dbconn."<BR>\n";
	echo "dbresult	= ".$this->dbresult."<BR>\n";
	echo "dbfetch	= ".$this->dbfetch."<BR>\n";
	echo "dbrnum	= ".$this->dbrnum."<BR>\n";
	echo "dbrow	= ".$this->dbrow."<BR>\n";
	echo "dbfnum	= ".$this->dbfnum."<BR>\n";
	echo "dbfields	= ".$this->dbfields."<BR>\n";
	for ($i = 0; $i < $this->dbfnum; $i++) {
		echo " ".$this->dbfields[$i];
	}
	echo "<BR>\n";
	echo "dbfset	= ".$this->dbfset."<BR>\n";
}

function dumper ($obj, $level = 0) {
	if (is_array($obj) || is_object($obj)) {
		reset($obj);
		while (list($key,$value) = each($obj)) {
			for($i = 0; $i < $level; $i++) {
				echo "  ";
			}
			echo "[$key] => '$value' <BR>\n";
			if (is_array($value) || is_object($value)) {
				$this->dumper($value, $level + 1);
			}
		}
	}
}

//////////////////////////////////////////////////////////////////////////
// accessor methods
//////////////////////////////////////////////////////////////////////////
function debug ($q = -999) {
	if ($q >= 0) {
		$this->debug = $q;
	}
	if ($this->debug > 0) {
		echo "RKO_DB::debug($this,$q)<BR>\n";
	}
	return $this->debug;
}

function query ($q = "") {
	if ($this->debug > 0) {
		echo "RKO_DB::query($this,$q)<BR>\n";
	}
	if ($q != "") {
		$this->query = $q;
	}
	return $this->query;
}

// returns copy of array of fields (or columns) from last query
// or sets which ones to use.  Must be used after the exec and before fetches
function fields ($q = "") {
	if ($this->debug > 0) {
		echo "RKO_DB::fields($this,$q)<BR>\n";
	}
	if (is_array($q)) {
		$this->dbfset = true;
		$this->dbfields = $q;
		$this->dbfnum = $q;
	}
	return $this->dbfields;
}

function errno () {
	if ($this->debug > 0) {
		echo "RKO_DB::errno($this)<BR>\n";
	}
	return $this->errno;
}

function errmsg () {
	if ($this->debug > 0) {
		echo "RKO_DB::errmsg($this)<BR>\n";
	}
	return $this->errmsg."\n";
}

//////////////////////////////////////////////////////////////////////////
// methods
//////////////////////////////////////////////////////////////////////////
// execute the query and return true or false
function exec () {
	if ($this->debug > 0) {
		echo "RKO_DB::exec($this)<BR>\n";
	}
	$this->dbresult = pg_query($this->dbconn,$this->query);
	if (!$this->dbresult) {
		$this->errmsg.="failed exec of query($this->query)";
		$this->errno=-2;
		return false;
	}
	$this->dbrow = 0;
	// can't rely on all RDBS to give num of rows
	$this->dbfnum = pg_num_fields($this->dbresult);
	$this->dbrnum = pg_num_rows($this->dbresult);
	$this->dbfields = array();
	for ($col = 0; $col < $this->dbfnum; $col++) {
		$this->dbfields[$col] = pg_field_name($this->dbresult,$col);
	}
	$this->dbfset = false;
	return true;
}

// fetch each row as an assoc array from the query result
// returns false when no more
function &fetch() {
	if ($this->debug > 0) {
		echo "RKO_DB::fetch($this)<BR>\n";
	}
	if ($this->dbrow >= $this->dbrnum) {
		// must stop fetch before end, else get nasty warning
		return false;
	}
	$this->dbfetch = pg_fetch_object($this->dbresult, $this->dbrow,
		PGSQL_ASSOC);
	$this->dbrow++;
	if ($this->dbfset) {
		$t = array();
		reset($this->dbfields);
		while ($key = next($this->dbfields)) {
			$t[$key] = $this->dbfetch[$key];
		}
		$this->dbfetch = $t;
	}
	return $this->dbfetch;
}

// returns an indexed array of associative arrays
function &fetch_array () {
	if ($this->debug > 0) {
		echo "RKO_DB::fetch_array($this)<BR>\n";
	}
	$t = array();
	while ($row = $this->fetch()) {
		$t[] = $row;
	}
	return $t;
}

// returns a simple value (good for getting counts or unique values
//   or executing simple SQL statements)
function sql($q) {
	if ($this->debug > 0) {
		echo "RKO_DB::sql($this,$q)<BR>\n";
	}
	if (! $this->query($q)) {
		print "RKO_DB::sql->query:".$this->errmsg()."<BR>\n"; 
		exit;
	}

	if (! $this->exec()) {
		print "RKO_DB::sql->exec:".$this->errmsg()."<BR>\n";
		exit;
	}
	if ($data = $this->fetch()) {
		list($k,$v) = each($data);
		return $v;
	} else {
		return;		// nothing (is the same as false)
	}
}

// return a hash array  of hash arrays
// from a SQL select relating columns
//	(col=>("col1"=>col1,"col1"=>col1,...),...)
// pass in multi_col("col", $sql)
//
function &multi_col ($col, $sql) {
	if ($this->debug > 0) {
		echo "RKO_DB::multi_col($this,$col,$sql)<BR>\n";
	}

	if (! $this->query($sql)) {
		print "RKO_DB::multi_col->query:".$this->errmsg()."<BR>\n"; 
		exit;
	}

	if (! $this->exec()) {
		print "RKO_DB::multi_col->exec:".$this->errmsg()."<BR>\n";
		exit;
	}

	$multi_col = array();

	// remember fetch returns an object ... can't access things like
	// an array
	while ($multi_col_data = $this->fetch()) {
		$mcd = (array) $multi_col_data;
		$multi_col[$mcd[$col]] = $mcd;
	}

	return $multi_col;		// return array reference
}

// return a hash array from a SQL select relating 2 of the columns (col1=>col2)
function &two_col ($col1, $col2, $sql) {
	if ($this->debug > 0) {
		echo "RKO_DB::two_col($this,$sql)<BR>\n";
	}


	if (! $this->query($sql)) {
		print "RKO_DB::two_col->query:".$this->errmsg()."<BR>\n"; 
		exit;
	}

	if (! $this->exec()) {
		print "RKO_DB::two_col->exec:".$this->errmsg()."<BR>\n";
		exit;
	}

	$two_col = array();
	while ($two_col_data = $this->fetch()) {
		while (list($k,$v) = each($two_col_data)) { $$k = $v; }
		$two_col[$$col1] = $$col2;
	}

	return $two_col;		// return array reference
}

// return a hash array from a SQL select that returns a single row (or should)
function &one_row ($sql) {
	if ($this->debug > 0) {
		echo "RKO_DB::one_row($this,$sql)<BR>\n";
	}

	if (! $this->query($sql)) {
		print "RKO_DB::one_row->query:".$this->errmsg()."<BR>\n"; 
		exit;
	}

	if (! $this->exec()) {
		print "RKO_DB::one_row->exec:".$this->errmsg()."<BR>\n";
		exit;
	}

	$one_row_data = array();
	$one_row_data = (array) $this->fetch();

	return $one_row_data;		// return array reference
}

// escapes the PostgreSQL special characters
function escape($q) {
	$qq = pg_escape_string($q);
	if ($this->debug > 0) {
		echo "RKO_DB::escape($this,$q)=>$qq<BR>\n";
	}
	return $qq;
}

// converts \'s to '		-- for select outputs
function dequote($q) {
	$qq = preg_replace("/\\\\'/","'",$q);
	if ($this->debug > 0) {
		echo "RKO_DB::dequote($this,$q)=>$qq<BR>\n";
	}
	return $qq;
}

// converts 's to \'		-- for update inputs
function quote($q) {
	$qq = preg_replace("/'/","''",$q);
	if ($this->debug > 0) {
		echo "RKO_DB::quote($this,$q)=>$qq<BR>\n";
	}
	return $qq;
}

}// end of RKO_DB
?>
