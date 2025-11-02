<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101233147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_instrument DROP FOREIGN KEY FK_D160843CCF11D9C');
        $this->addSql('ALTER TABLE student_instrument DROP FOREIGN KEY FK_D160843CCB944F1A');
        $this->addSql('ALTER TABLE teacher_instrument DROP FOREIGN KEY FK_B6A0CB7DCF11D9C');
        $this->addSql('ALTER TABLE teacher_instrument DROP FOREIGN KEY FK_B6A0CB7D41807E1D');
        $this->addSql('DROP TABLE student_instrument');
        $this->addSql('DROP TABLE teacher_instrument');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_instrument (student_id INT NOT NULL, instrument_id INT NOT NULL, INDEX IDX_D160843CCB944F1A (student_id), INDEX IDX_D160843CCF11D9C (instrument_id), PRIMARY KEY(student_id, instrument_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE teacher_instrument (teacher_id INT NOT NULL, instrument_id INT NOT NULL, INDEX IDX_B6A0CB7D41807E1D (teacher_id), INDEX IDX_B6A0CB7DCF11D9C (instrument_id), PRIMARY KEY(teacher_id, instrument_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE student_instrument ADD CONSTRAINT FK_D160843CCF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_instrument ADD CONSTRAINT FK_D160843CCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_instrument ADD CONSTRAINT FK_B6A0CB7DCF11D9C FOREIGN KEY (instrument_id) REFERENCES instrument (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_instrument ADD CONSTRAINT FK_B6A0CB7D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
