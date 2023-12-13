# blog_project

Coded using Symfony 6.4 in VSCode
Database PHPMyAdmin

Triggers in Database (handling reports and verified):
AFTER UPDATE on blog
BEGIN
    IF NEW.verified = '1' THEN
        DELETE FROM reports_b
        WHERE blog_id_id = NEW.id;
    END IF;
END

AFTER UPDATE on comment
BEGIN
    IF NEW.verified = '1' THEN
        DELETE FROM reports_c
        WHERE comment_id_id = NEW.id;
    END IF;
END

AFTER INSERT on reports_b and reports_c
BEGIN
    DECLARE blog_count INT;

    SELECT COUNT(*) INTO blog_count
    FROM reports_b
    WHERE blog_id_id = NEW.blog_id_id;

    IF blog_count = 10 THEN
        UPDATE blog
        SET hidden = '1'
        WHERE id = NEW.blog_id_id;
    END IF;
END
