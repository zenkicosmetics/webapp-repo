ALTER TABLE `customers`
 CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL;


DELIMITER //
DROP TRIGGER IF EXISTS my_require_email_on_update_trigger//

CREATE TRIGGER my_require_email_on_update_trigger
    AFTER INSERT ON `customers`
    FOR EACH ROW
BEGIN
    IF NEW.email = ''THEN
                SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'blank value in email field';
        END IF;
END//
DELIMITER ;

DELIMITER //
DROP TRIGGER IF EXISTS my_require_email_on_insert_trigger//

CREATE TRIGGER my_require_email_on_insert_trigger
    AFTER INSERT ON `customers`
    FOR EACH ROW
BEGIN
    IF NEW.email = ''THEN
                SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'blank value in email field';
        END IF;
END//
DELIMITER ;