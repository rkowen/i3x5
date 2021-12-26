/* SQL script to update the tables converting from straight text
 * to encrypted text.
 * R.K. Owen, Ph.D. 20211208
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

BEGIN
	CREATE EXTENSION pgcrypto;
EXCEPTION
	-- ERROR:  42710: extension "pgcrypto" already exists
	WHEN duplicate_object THEN
	-- do nothing --
		RAISE NOTICE 'pgcrypto already installed';
END;

	UPDATE i3x5_userpass
	SET	xpasswd_admin	= pgp_sym_encrypt(passwd_admin,cryptstr),
		xpasswd_w	= pgp_sym_encrypt(passwd_w,cryptstr),
		xpasswd_a	= pgp_sym_encrypt(passwd_a,cryptstr),
		xpasswd_r	= pgp_sym_encrypt(passwd_r,cryptstr);

	UPDATE i3x5_userpass
	SET	passwd_admin	= NULL,
		passwd_w	= NULL,
		passwd_a	= NULL,
		passwd_r	= NULL;

	RAISE NOTICE 'FINISHED SUCCESSFULLY';
END $$;
