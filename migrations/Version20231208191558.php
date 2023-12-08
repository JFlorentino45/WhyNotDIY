<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208191558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DD6DE06A6');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DD6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DD6DE06A6');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DD6DE06A6 FOREIGN KEY (comment_id_id) REFERENCES comments (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
