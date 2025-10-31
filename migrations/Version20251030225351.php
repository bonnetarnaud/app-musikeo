<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030225351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, instrument_id INT NOT NULL, teacher_id INT NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_169E6FB9CF11D9C (instrument_id), INDEX IDX_169E6FB941807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enrollment (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, course_id INT NOT NULL, date_enrolled DATE NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_DBDCD7E1CB944F1A (student_id), INDEX IDX_DBDCD7E1591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instrument (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, room_id INT NOT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, INDEX IDX_F87474F3591CC992 (course_id), INDEX IDX_F87474F354177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, date DATE NOT NULL, method VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_6D28840DCB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, capacity INT NOT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, date_of_birth DATE DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_instrument (student_id INT NOT NULL, instrument_id INT NOT NULL, INDEX IDX_D160843CCB944F1A (student_id), INDEX IDX_D160843CCF11D9C (instrument_id), PRIMARY KEY(student_id, instrument_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher (id INT NOT NULL, phone VARCHAR(20) DEFAULT NULL, biography LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher_instrument (teacher_id INT NOT NULL, instrument_id INT NOT NULL, INDEX IDX_B6A0CB7D41807E1D (teacher_id), INDEX IDX_B6A0CB7DCF11D9C (instrument_id), PRIMARY KEY(teacher_id, instrument_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9CF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB941807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F354177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_instrument ADD CONSTRAINT FK_D160843CCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_instrument ADD CONSTRAINT FK_D160843CCF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_instrument ADD CONSTRAINT FK_B6A0CB7D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_instrument ADD CONSTRAINT FK_B6A0CB7DCF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD firstname VARCHAR(100) NOT NULL, ADD lastname VARCHAR(100) NOT NULL, ADD user_type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9CF11D9C');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB941807E1D');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1CB944F1A');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1591CC992');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3591CC992');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F354177093');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DCB944F1A');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33BF396750');
        $this->addSql('ALTER TABLE student_instrument DROP FOREIGN KEY FK_D160843CCB944F1A');
        $this->addSql('ALTER TABLE student_instrument DROP FOREIGN KEY FK_D160843CCF11D9C');
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D5BF396750');
        $this->addSql('ALTER TABLE teacher_instrument DROP FOREIGN KEY FK_B6A0CB7D41807E1D');
        $this->addSql('ALTER TABLE teacher_instrument DROP FOREIGN KEY FK_B6A0CB7DCF11D9C');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE enrollment');
        $this->addSql('DROP TABLE instrument');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_instrument');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE teacher_instrument');
        $this->addSql('ALTER TABLE user DROP firstname, DROP lastname, DROP user_type');
    }
}
