<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101231606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE instrument_rental (id INT AUTO_INCREMENT NOT NULL, instrument_id INT NOT NULL, student_id INT NOT NULL, organization_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, monthly_price NUMERIC(8, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_22AFC135CF11D9C (instrument_id), INDEX IDX_22AFC135CB944F1A (student_id), INDEX IDX_22AFC13532C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE instrument_rental ADD CONSTRAINT FK_22AFC135CF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id)');
        $this->addSql('ALTER TABLE instrument_rental ADD CONSTRAINT FK_22AFC135CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE instrument_rental ADD CONSTRAINT FK_22AFC13532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9CF11D9C');
        $this->addSql('DROP INDEX IDX_169E6FB9CF11D9C ON course');
        $this->addSql('ALTER TABLE course ADD description VARCHAR(255) DEFAULT NULL, DROP instrument_id');
        $this->addSql('ALTER TABLE instrument ADD current_renter_id INT DEFAULT NULL, ADD serial_number VARCHAR(100) DEFAULT NULL, ADD brand VARCHAR(100) DEFAULT NULL, ADD model VARCHAR(100) DEFAULT NULL, ADD is_rentable TINYINT(1) NOT NULL, ADD is_currently_rented TINYINT(1) NOT NULL, ADD rental_start_date DATE DEFAULT NULL, ADD additional_info LONGTEXT DEFAULT NULL, ADD `condition` VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD39AF14E9 FOREIGN KEY (current_renter_id) REFERENCES student (id)');
        $this->addSql('CREATE INDEX IDX_3CBF69DD39AF14E9 ON instrument (current_renter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instrument_rental DROP FOREIGN KEY FK_22AFC135CF11D9C');
        $this->addSql('ALTER TABLE instrument_rental DROP FOREIGN KEY FK_22AFC135CB944F1A');
        $this->addSql('ALTER TABLE instrument_rental DROP FOREIGN KEY FK_22AFC13532C8A3DE');
        $this->addSql('DROP TABLE instrument_rental');
        $this->addSql('ALTER TABLE course ADD instrument_id INT NOT NULL, DROP description');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9CF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_169E6FB9CF11D9C ON course (instrument_id)');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DD39AF14E9');
        $this->addSql('DROP INDEX IDX_3CBF69DD39AF14E9 ON instrument');
        $this->addSql('ALTER TABLE instrument DROP current_renter_id, DROP serial_number, DROP brand, DROP model, DROP is_rentable, DROP is_currently_rented, DROP rental_start_date, DROP additional_info, DROP `condition`');
    }
}
