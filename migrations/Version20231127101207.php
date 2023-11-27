<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127101207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_notification ADD user_id INT DEFAULT NULL, ADD comment_id INT DEFAULT NULL, ADD blog_id INT DEFAULT NULL, DROP is_sign_up, DROP is_comment, DROP is_blog, DROP identifier');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C615D427A76ED395 ON admin_notification (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C615D427F8697D13 ON admin_notification (comment_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C615D427DAE07E97 ON admin_notification (blog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427A76ED395');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427F8697D13');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427DAE07E97');
        $this->addSql('DROP INDEX UNIQ_C615D427A76ED395 ON admin_notification');
        $this->addSql('DROP INDEX UNIQ_C615D427F8697D13 ON admin_notification');
        $this->addSql('DROP INDEX UNIQ_C615D427DAE07E97 ON admin_notification');
        $this->addSql('ALTER TABLE admin_notification ADD is_sign_up TINYINT(1) NOT NULL, ADD is_comment TINYINT(1) NOT NULL, ADD is_blog TINYINT(1) NOT NULL, ADD identifier INT NOT NULL, DROP user_id, DROP comment_id, DROP blog_id');
    }
}
