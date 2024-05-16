<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516132707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP FOREIGN KEY FK_4D7E68545538B3E5');
        $this->addSql('DROP INDEX IDX_4D7E68545538B3E5 ON apartment');
        $this->addSql('ALTER TABLE apartment CHANGE id_building_id building_id INT NOT NULL');
        $this->addSql('ALTER TABLE apartment ADD CONSTRAINT FK_4D7E68544D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE INDEX IDX_4D7E68544D2A7E12 ON apartment (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP FOREIGN KEY FK_4D7E68544D2A7E12');
        $this->addSql('DROP INDEX IDX_4D7E68544D2A7E12 ON apartment');
        $this->addSql('ALTER TABLE apartment CHANGE building_id id_building_id INT NOT NULL');
        $this->addSql('ALTER TABLE apartment ADD CONSTRAINT FK_4D7E68545538B3E5 FOREIGN KEY (id_building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4D7E68545538B3E5 ON apartment (id_building_id)');
    }
}
