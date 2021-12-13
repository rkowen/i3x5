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

	ALTER TABLE i3x5_batch ADD COLUMN crypted BOOLEAN DEFAULT false;

	ALTER TABLE i3x5_cards ADD COLUMN xtitle BYTEA;
	ALTER TABLE i3x5_cards ADD COLUMN xcard BYTEA;
	ALTER TABLE i3x5_cards ADD COLUMN crypted BOOLEAN DEFAULT false;

	RAISE NOTICE 'FINISHED SUCCESSFULLY';
END $$;
