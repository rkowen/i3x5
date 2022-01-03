--
-- re-enable the table triggers (foreign key constraints)
-- after a data load
--
ALTER TABLE i3x5_userpass	ENABLE TRIGGER ALL;
ALTER TABLE i3x5_batch		ENABLE TRIGGER ALL;
ALTER TABLE i3x5_help		ENABLE TRIGGER ALL;
ALTER TABLE i3x5_cards		ENABLE TRIGGER ALL;
