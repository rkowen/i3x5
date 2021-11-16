BEGIN
	CREATE EXTENSION pgcrypto;
EXCEPTION
	WHEN OTHERS THEN
	-- do nothing --
		RAISE NOTICE 'pgcrypto already installed';
END:

DROP TABLE i3x5_cards;
DROP SEQUENCE i3x5_batch_bid_seq;
DROP TABLE i3x5_batch;
DROP SEQUENCE i3x5_cards_id_seq;
DROP TABLE i3x5_userpass;
DROP SEQUENCE i3x5_userpass_uid_seq;
DROP TABLE i3x5_crypt;
DROP TABLE i3x5_help;

CREATE TABLE i3x5_userpass (
	uid		SERIAL,			-- internal userid
	project		TEXT DEFAULT '3x5 Cards',-- project name
	username	TEXT,			-- username
	passwd_admin	BYTEA,			-- admin password
	passwd_w	BYTEA,			-- full-write password
	passwd_a	BYTEA,			-- append-only password
	passwd_r	BYTEA,			-- read-only password
	author		TEXT,			-- name
	email		TEXT,			-- email for notification
	challenge	TEXT,			-- secure reminder
	response	TEXT,			-- secure authentication
	crypthint	TEXT,			-- hint for data encryption
	createdate	timestamp DEFAULT now(),
	moddate		timestamp DEFAULT now(),
	PRIMARY KEY(uid)
);

-- forces username to be unique
CREATE UNIQUE INDEX i3x5_userpass_username ON i3x5_userpass (username);

CREATE TABLE i3x5_batch (
	bid		SERIAL,		-- batch ID #
	uid		INT,		-- uid of owner (from userpass)
	batch		TEXT,		-- batch name
	batch_help	TEXT,		-- batch name
	rid		INT,		-- relate to another batch for user
					-- skips rest if not null
	num_name	TEXT,		-- num field name
	num_help	TEXT DEFAULT 'nothing helpful',	
					-- num field helpful description
	title_name	TEXT,		-- title field name
	title_help	TEXT DEFAULT 'nothing helpful',	
					-- title field helpful description
	card_name	TEXT,		-- card field name
	card_help	TEXT DEFAULT 'nothing helpful',	
					-- card field helpful description
	createdate	timestamp DEFAULT now(),
	moddate		timestamp DEFAULT now(),
	PRIMARY KEY(bid),
	FOREIGN KEY(uid) REFERENCES i3x5_userpass(uid)
);

CREATE TABLE i3x5_cards (
	id		SERIAL,		-- internal ID #
	bid		INT,		-- batch id
	rid		INT,		-- relate to another card for user
					-- skips rest if not null
	num		INT8,		-- user numbering of card
	title		TEXT,		-- title of card
	card		TEXT,		-- card contents
	formatted	BOOLEAN DEFAULT false,	-- use <PRE> formatting
	createdate	timestamp DEFAULT now(),
	moddate		timestamp DEFAULT now(),
	PRIMARY KEY (id),
	FOREIGN KEY(bid) REFERENCES i3x5_batch(bid)
);

CREATE TABLE i3x5_help (
	key		TEXT,		-- keyword
	help		TEXT,		-- help text
	PRIMARY KEY(key)
);

--
-- create triggers for some automatic actions
--  updating moddate for any update
--

DROP TRIGGER ut_userpass ON i3x5_userpass;
DROP FUNCTION uf_userpass();
DROP TRIGGER ut_batch ON i3x5_batch;
DROP FUNCTION uf_batch();
DROP TRIGGER ut_cards ON i3x5_cards;
DROP FUNCTION uf_cards();

CREATE FUNCTION uf_userpass()
RETURNS TRIGGER AS '
BEGIN
	NEW.moddate:=now();
	RETURN NEW;
END;
' LANGUAGE 'plpgsql';

CREATE TRIGGER ut_userpass BEFORE UPDATE
	ON i3x5_userpass FOR EACH ROW
	EXECUTE PROCEDURE uf_userpass();

CREATE FUNCTION uf_batch()
RETURNS TRIGGER AS '
BEGIN
	NEW.moddate:=now();
	RETURN NEW;
END;
' LANGUAGE 'plpgsql';

CREATE TRIGGER ut_batch BEFORE UPDATE
	ON i3x5_batch FOR EACH ROW
	EXECUTE PROCEDURE uf_batch();

CREATE FUNCTION uf_cards()
RETURNS TRIGGER AS '
BEGIN
	IF NEW.num <> OLD.num OR NEW.title <> OLD.title
	OR NEW.card <> OLD.card THEN
		NEW.moddate:=now();
	END IF;
	RETURN NEW;
END;
' LANGUAGE 'plpgsql';

CREATE TRIGGER ut_cards BEFORE UPDATE
	ON i3x5_cards FOR EACH ROW
	EXECUTE PROCEDURE uf_cards();

--
--CREATE USER "www-data"
--WITH PASSWORD cardaccess
--NOCREATEDB NOCREATEUSER;
--
---- i3x5 Card administrator
INSERT INTO i3x5_userpass
	(uid,username,passwd_admin,author,email) VALUES
	(0,'root','dogstar','R.K. Owen,Ph.D.','rk@owen.sj.ca.us');

GRANT SELECT,INSERT,UPDATE,DELETE ON i3x5_cards TO "www-data";
GRANT SELECT,INSERT,UPDATE,DELETE ON i3x5_batch TO "www-data";
GRANT SELECT,INSERT,UPDATE ON i3x5_userpass TO "www-data";
GRANT SELECT,UPDATE ON i3x5_batch_bid_seq TO "www-data";
GRANT SELECT,UPDATE ON i3x5_cards_id_seq TO "www-data";
GRANT SELECT,UPDATE ON i3x5_userpass_uid_seq TO "www-data";
GRANT SELECT ON i3x5_help TO "www-data";
