<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607075033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, picture VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, duration VARCHAR(40) DEFAULT NULL, INDEX IDX_3BAE0AA74D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, title VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, picture VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_1DD399504D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA74D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD399504D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA74D2A7E12');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD399504D2A7E12');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE news');
    }
}
