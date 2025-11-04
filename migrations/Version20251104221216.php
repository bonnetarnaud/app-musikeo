<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104221216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__admin AS SELECT id FROM admin');
        $this->addSql('DROP TABLE admin');
        $this->addSql('CREATE TABLE admin (id INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO admin (id) SELECT id FROM __temp__admin');
        $this->addSql('DROP TABLE __temp__admin');
        $this->addSql('CREATE TEMPORARY TABLE __temp__student AS SELECT id, date_of_birth, address, phone FROM student');
        $this->addSql('DROP TABLE student');
        $this->addSql('CREATE TABLE student (id INTEGER NOT NULL, date_of_birth DATE DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO student (id, date_of_birth, address, phone) SELECT id, date_of_birth, address, phone FROM __temp__student');
        $this->addSql('DROP TABLE __temp__student');
        $this->addSql('CREATE TEMPORARY TABLE __temp__teacher AS SELECT id, phone, biography, specialties FROM teacher');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('CREATE TABLE teacher (id INTEGER NOT NULL, phone VARCHAR(20) DEFAULT NULL, biography CLOB DEFAULT NULL, specialties VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO teacher (id, phone, biography, specialties) SELECT id, phone, biography, specialties FROM __temp__teacher');
        $this->addSql('DROP TABLE __temp__teacher');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__admin AS SELECT id FROM admin');
        $this->addSql('DROP TABLE admin');
        $this->addSql('CREATE TABLE admin (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO admin (id) SELECT id FROM __temp__admin');
        $this->addSql('DROP TABLE __temp__admin');
        $this->addSql('CREATE TEMPORARY TABLE __temp__student AS SELECT id, date_of_birth, address, phone FROM student');
        $this->addSql('DROP TABLE student');
        $this->addSql('CREATE TABLE student (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_of_birth DATE DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO student (id, date_of_birth, address, phone) SELECT id, date_of_birth, address, phone FROM __temp__student');
        $this->addSql('DROP TABLE __temp__student');
        $this->addSql('CREATE TEMPORARY TABLE __temp__teacher AS SELECT id, phone, biography, specialties FROM teacher');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('CREATE TABLE teacher (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, phone VARCHAR(20) DEFAULT NULL, biography CLOB DEFAULT NULL, specialties VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO teacher (id, phone, biography, specialties) SELECT id, phone, biography, specialties FROM __temp__teacher');
        $this->addSql('DROP TABLE __temp__teacher');
    }
}
