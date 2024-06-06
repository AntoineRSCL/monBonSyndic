<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606131452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apartment (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, reference VARCHAR(5) NOT NULL, floor INT NOT NULL, quota1 INT NOT NULL, quota2 INT NOT NULL, INDEX IDX_4D7E68544D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE owner (id INT AUTO_INCREMENT NOT NULL, apartment_id INT NOT NULL, person_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, INDEX IDX_CF60E67C176DFE85 (apartment_id), INDEX IDX_CF60E67C217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, name VARCHAR(60) NOT NULL, firstname VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(25) DEFAULT NULL, address VARCHAR(255) NOT NULL, number VARCHAR(10) NOT NULL, zip VARCHAR(20) NOT NULL, locality VARCHAR(70) NOT NULL, country VARCHAR(50) NOT NULL, optin TINYINT(1) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_34DCD1764D2A7E12 (building_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, question VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_AD5F9BFC4D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, survey_id INT NOT NULL, answer VARCHAR(255) NOT NULL, INDEX IDX_5A108564217BBB47 (person_id), INDEX IDX_5A108564B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apartment ADD CONSTRAINT FK_4D7E68544D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE owner ADD CONSTRAINT FK_CF60E67C176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartment (id)');
        $this->addSql('ALTER TABLE owner ADD CONSTRAINT FK_CF60E67C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1764D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE building ADD picture VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment DROP FOREIGN KEY FK_4D7E68544D2A7E12');
        $this->addSql('ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67C176DFE85');
        $this->addSql('ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67C217BBB47');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1764D2A7E12');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC4D2A7E12');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564217BBB47');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564B3FE509D');
        $this->addSql('DROP TABLE apartment');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE building DROP picture');
    }
}
