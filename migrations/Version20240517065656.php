<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517065656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67C176DFE85');
        $this->addSql('ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67C217BBB47');
        $this->addSql('DROP TABLE owner');
        $this->addSql('ALTER TABLE person ADD roles JSON NOT NULL, CHANGE phone phone VARCHAR(25) DEFAULT NULL, CHANGE username username VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON person (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE owner (id INT AUTO_INCREMENT NOT NULL, apartment_id INT NOT NULL, person_id INT NOT NULL, start DATE NOT NULL, end DATE DEFAULT NULL, INDEX IDX_CF60E67C176DFE85 (apartment_id), INDEX IDX_CF60E67C217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE owner ADD CONSTRAINT FK_CF60E67C176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE owner ADD CONSTRAINT FK_CF60E67C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USERNAME ON person');
        $this->addSql('ALTER TABLE person DROP roles, CHANGE username username VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(25) NOT NULL');
    }
}
