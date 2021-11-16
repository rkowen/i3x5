/* SQL script to update the tables from version 0.7.1 to version 0.8.0
 * - the major change is the addition of $db_crypt to encrypt the
 * - project passwords (and prepare for selective data encryption).
 * R.K. Owen, Ph.D. 20211115
 *
 ************************************************************************
 * YOU MUST CHANGE THE ENCRYPT PHRASE BELOW TO YOUR OWN CHOICE !!!!
 ************************************************************************
 */
DO $$
DECLARE
	cryptstr TEXT	:= 'SomeString';	/* CHANGE ME */
BEGIN
	IF cryptstr = 'SomeString' THEN
		RAISE EXCEPTION 'You need to change the cryptstring!';
	END IF;

	ALTER TABLE i3x5_userpass ADD COLUMN xpasswd_admin BYTEA;
	ALTER TABLE i3x5_userpass ADD COLUMN xpasswd_w BYTEA;
	ALTER TABLE i3x5_userpass ADD COLUMN xpasswd_a BYTEA;
	ALTER TABLE i3x5_userpass ADD COLUMN xpasswd_r BYTEA;

BEGIN
	CREATE EXTENSION pgcrypto;
EXCEPTION
	WHEN OTHERS THEN
	-- do nothing --
		RAISE NOTICE 'pgcrypto already installed';
END;
	UPDATE i3x5_userpass
	SET	xpasswd_admin	= pgp_sym_encrypt(passwd_admin,cryptstr),
		xpasswd_w	= pgp_sym_encrypt(passwd_w,cryptstr),
		xpasswd_a	= pgp_sym_encrypt(passwd_a,cryptstr),
		xpasswd_r	= pgp_sym_encrypt(passwd_r,cryptstr);

	ALTER TABLE i3x5_userpass DROP COLUMN passwd_admin;
	ALTER TABLE i3x5_userpass DROP COLUMN passwd_w;
	ALTER TABLE i3x5_userpass DROP COLUMN passwd_a;
	ALTER TABLE i3x5_userpass DROP COLUMN passwd_r;

	ALTER TABLE i3x5_userpass RENAME COLUMN xpasswd_admin
		TO passwd_admin;
	ALTER TABLE i3x5_userpass RENAME COLUMN xpasswd_w
		TO passwd_w;
	ALTER TABLE i3x5_userpass RENAME COLUMN xpasswd_a
		TO passwd_a;
	ALTER TABLE i3x5_userpass RENAME COLUMN xpasswd_r
		TO passwd_r;

	RAISE NOTICE 'FINISHED SUCCESSFULLY';
END $$;
