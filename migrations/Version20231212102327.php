<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212102327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, comment_id_id INT DEFAULT NULL, blog_id_id INT DEFAULT NULL, person_id_id INT DEFAULT NULL, INDEX IDX_F11FA7459D86650F (user_id_id), INDEX IDX_F11FA745D6DE06A6 (comment_id_id), INDEX IDX_F11FA7458FABDD9F (blog_id_id), INDEX IDX_F11FA745D3728193 (person_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7459D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7458FABDD9F FOREIGN KEY (blog_id_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745D3728193 FOREIGN KEY (person_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA7459D86650F');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745D6DE06A6');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA7458FABDD9F');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745D3728193');
        $this->addSql('DROP TABLE reports');
    }
}
