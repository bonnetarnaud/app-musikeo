<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102212959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pre_registration (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(20) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, address LONGTEXT DEFAULT NULL, parent_name VARCHAR(100) DEFAULT NULL, parent_email VARCHAR(180) DEFAULT NULL, parent_phone VARCHAR(20) DEFAULT NULL, interested_instrument VARCHAR(50) NOT NULL, level VARCHAR(50) NOT NULL, message LONGTEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, contacted_at DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_A2FEF1B932C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pre_registration ADD CONSTRAINT FK_A2FEF1B932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pre_registration DROP FOREIGN KEY FK_A2FEF1B932C8A3DE');
        $this->addSql('DROP TABLE pre_registration');
    }
}
