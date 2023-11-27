<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127101639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427A76ED395');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427DAE07E97');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427F8697D13');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427A76ED395');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427F8697D13');
        $this->addSql('ALTER TABLE admin_notification DROP FOREIGN KEY FK_C615D427DAE07E97');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427F8697D13 FOREIGN KEY (comment_id) REFERENCES comments (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE admin_notification ADD CONSTRAINT FK_C615D427DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
