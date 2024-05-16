<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516135425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person ADD building_id INT DEFAULT NULL, ADD username VARCHAR(255) NOT NULL, ADD password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1764D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1764D2A7E12 ON person (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1764D2A7E12');
        $this->addSql('DROP INDEX IDX_34DCD1764D2A7E12 ON person');
        $this->addSql('ALTER TABLE person DROP building_id, DROP username, DROP password');
    }
}
