<?php
//////////////////////////////////////////////////////////////////////////
//
// DESC: db_connect.inc
// DESC: DB connection string
// DESC: uses env.vars for passing some of the values
//
//////////////////////////////////////////////////////////////////////////

$DB_URL=getenv("DATABASE_URL");
if (!$DB_URL) {
	$db_pass = "SomePassword";
	$db_connect = "user=www-data password=$db_pass dbname=i3x5 port=5432 host=localhost";
} else {
	$dbarr = parse_url($DB_URL);
	$db_pass = $dbarr["pass"];
	$db_connect = $DB_URL;
}
$DB_CRYPT=getenv("DB_CRYPT");
if (!$DB_CRYPT) {
	$db_encode = false;
	$db_crypt = "SomeString";
} else {
	$db_encode = true;
	$db_crypt = $DB_CRYPT;
}
?>
