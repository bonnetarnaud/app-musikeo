<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101221022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE course SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE course MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB932C8A3DE ON course (organization_id)');
        
        $this->addSql('ALTER TABLE enrollment ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE enrollment SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE enrollment MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E132C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E132C8A3DE ON enrollment (organization_id)');
        
        $this->addSql('ALTER TABLE instrument ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE instrument SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE instrument MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE instrument ADD CONSTRAINT FK_3CBF69DD32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_3CBF69DD32C8A3DE ON instrument (organization_id)');
        
        $this->addSql('ALTER TABLE lesson ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE lesson SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE lesson MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_F87474F332C8A3DE ON lesson (organization_id)');
        
        $this->addSql('ALTER TABLE payment ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE payment SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE payment MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D32C8A3DE ON payment (organization_id)');
        
        $this->addSql('ALTER TABLE room ADD organization_id INT DEFAULT 1');
        $this->addSql('UPDATE room SET organization_id = 1 WHERE organization_id IS NULL');
        $this->addSql('ALTER TABLE room MODIFY organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_729F519B32C8A3DE ON room (organization_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB932C8A3DE');
        $this->addSql('DROP INDEX IDX_169E6FB932C8A3DE ON course');
        $this->addSql('ALTER TABLE course DROP organization_id');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F332C8A3DE');
        $this->addSql('DROP INDEX IDX_F87474F332C8A3DE ON lesson');
        $this->addSql('ALTER TABLE lesson DROP organization_id');
        $this->addSql('ALTER TABLE instrument DROP FOREIGN KEY FK_3CBF69DD32C8A3DE');
        $this->addSql('DROP INDEX IDX_3CBF69DD32C8A3DE ON instrument');
        $this->addSql('ALTER TABLE instrument DROP organization_id');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D32C8A3DE');
        $this->addSql('DROP INDEX IDX_6D28840D32C8A3DE ON payment');
        $this->addSql('ALTER TABLE payment DROP organization_id');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E132C8A3DE');
        $this->addSql('DROP INDEX IDX_DBDCD7E132C8A3DE ON enrollment');
        $this->addSql('ALTER TABLE enrollment DROP organization_id');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B32C8A3DE');
        $this->addSql('DROP INDEX IDX_729F519B32C8A3DE ON room');
        $this->addSql('ALTER TABLE room DROP organization_id');
    }
}
