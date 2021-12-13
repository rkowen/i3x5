/* SQL script to update the tables converting from encrypted text
 * to plain text.
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
	WHEN OTHERS THEN
	-- do nothing --
		RAISE NOTICE 'pgcrypto already installed';
END;
	UPDATE i3x5_userpass
	SET	passwd_admin	= pgp_sym_decrypt(xpasswd_admin,cryptstr),
		passwd_w	= pgp_sym_decrypt(xpasswd_w,cryptstr),
		passwd_a	= pgp_sym_decrypt(xpasswd_a,cryptstr),
		passwd_r	= pgp_sym_decrypt(xpasswd_r,cryptstr);

	UPDATE i3x5_userpass
	SET	xpasswd_admin	= NULL,
		xpasswd_w	= NULL,
		xpasswd_a	= NULL,
		xpasswd_r	= NULL;

	RAISE NOTICE 'FINISHED SUCCESSFULLY';
END $$;
