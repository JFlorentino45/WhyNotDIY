<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212131127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports_b (id INT AUTO_INCREMENT NOT NULL, reporter_id_id INT NOT NULL, blog_id_id INT NOT NULL, INDEX IDX_1AFA2F04D6B1FFA1 (reporter_id_id), INDEX IDX_1AFA2F048FABDD9F (blog_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports_c (id INT AUTO_INCREMENT NOT NULL, reporter_id_id INT NOT NULL, comment_id_id INT NOT NULL, INDEX IDX_6DFD1F92D6B1FFA1 (reporter_id_id), INDEX IDX_6DFD1F92D6DE06A6 (comment_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F04D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F048FABDD9F FOREIGN KEY (blog_id_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id)');
        $this->addSql('ALTER TABLE blog ADD hidden TINYINT(1) DEFAULT NULL, ADD verified TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE comments ADD hidden TINYINT(1) DEFAULT NULL, ADD verified TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F04D6B1FFA1');
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F048FABDD9F');
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6B1FFA1');
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6DE06A6');
        $this->addSql('DROP TABLE reports_b');
        $this->addSql('DROP TABLE reports_c');
        $this->addSql('ALTER TABLE blog DROP hidden, DROP verified');
        $this->addSql('ALTER TABLE comments DROP hidden, DROP verified');
    }
}
