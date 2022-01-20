<?php
if (array_key_exists("DATABASE_URL",$_SERVER)) {
	print "DATABASE_URL exists</br>\n";
	unset($_SERVER["DATABASE_URL"]);
	unset($_ENV["DATABASE_URL"]);
	if (getenv("DATABASE_URL")) {
		putenv("DATABASE_URL=xxxxxxxxx");
	}
} else {
	print "DATABASE_URL does not exists!</br>\n";
}
if (array_key_exists("DB_CRYPT",$_SERVER)) {
	print "DB_CRYPT exists</br>\n";
	unset($_SERVER["DB_CRYPT"]);
	unset($_ENV["DB_CRYPT"]);
	if (getenv("DB_CRYPT")) {
		putenv("DB_CRYPT=xxxxxxxxx");
	}
} else {
	print "DB_CRYPT does not exists!</br>\n";
}
	phpinfo(INFO_VARIABLES); 
?>
