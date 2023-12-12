<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231212220002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE hidden hidden TINYINT(1) NOT NULL, CHANGE verified verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F048FABDD9F');
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F04D6B1FFA1');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F048FABDD9F FOREIGN KEY (blog_id_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F04D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6B1FFA1');
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6DE06A6');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6B1FFA1');
        $this->addSql('ALTER TABLE reports_c DROP FOREIGN KEY FK_6DFD1F92D6DE06A6');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reports_c ADD CONSTRAINT FK_6DFD1F92D6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F04D6B1FFA1');
        $this->addSql('ALTER TABLE reports_b DROP FOREIGN KEY FK_1AFA2F048FABDD9F');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F04D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE reports_b ADD CONSTRAINT FK_1AFA2F048FABDD9F FOREIGN KEY (blog_id_id) REFERENCES blog (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE comments CHANGE hidden hidden TINYINT(1) DEFAULT NULL, CHANGE verified verified TINYINT(1) DEFAULT NULL');
    }
}
