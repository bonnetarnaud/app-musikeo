<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101220545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(100) NOT NULL, address LONGTEXT DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, slug VARCHAR(100) NOT NULL, timezone VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, subscription_plan VARCHAR(50) NOT NULL, max_students INT NOT NULL, max_teachers INT NOT NULL, max_admins INT NOT NULL, UNIQUE INDEX UNIQ_C1EE637C989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Créer une organisation demo par défaut
        $this->addSql("INSERT INTO organization (name, type, email, slug, timezone, created_at, is_active, subscription_plan, max_students, max_teachers, max_admins) VALUES ('École de Musique Demo', 'school', 'demo@musikeo.com', 'demo', 'Europe/Paris', NOW(), 1, 'free', 30, 5, 1)");
        
        // Ajouter la colonne organization_id avec une valeur par défaut temporaire
        $this->addSql('ALTER TABLE user ADD organization_id INT NOT NULL DEFAULT 1');
        
        // Ajouter la contrainte de clé étrangère
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64932C8A3DE ON user (organization_id)');
        
        // Supprimer la valeur par défaut maintenant que tous les utilisateurs ont une organisation
        $this->addSql('ALTER TABLE user ALTER COLUMN organization_id DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64932C8A3DE');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP INDEX IDX_8D93D64932C8A3DE ON user');
        $this->addSql('ALTER TABLE user DROP organization_id');
    }
}
