--
-- disable table triggers (foreign key constraints)
-- to allow data loads
--
ALTER TABLE i3x5_userpass	DISABLE TRIGGER ALL;
ALTER TABLE i3x5_batch		DISABLE TRIGGER ALL;
ALTER TABLE i3x5_help		DISABLE TRIGGER ALL;
ALTER TABLE i3x5_cards		DISABLE TRIGGER ALL;
