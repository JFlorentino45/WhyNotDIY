# WhyNotDIY

Coded using Symfony 6.4 in VSCode

Database: PHPMyAdmin

Triggers in Database (handling reports and verified):

(AFTER UPDATE on post and comment)

    BEGIN
    IF NEW.verified = '1' THEN
        DELETE FROM reports_b
        WHERE post_id_id = NEW.id;
    END IF;
    END

(AFTER INSERT on reports_b and reports_c)

    BEGIN
    
    DECLARE post_count INT;

    SELECT COUNT(*) INTO post_count
    FROM reports_b
    WHERE post_id_id = NEW.post_id_id;

    IF post_count = 10 THEN
        UPDATE post
        SET hidden = '1'
        WHERE id = NEW.post_id_id;
    END IF;
    END
