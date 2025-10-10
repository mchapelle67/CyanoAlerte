<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251010140732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alert (id INT AUTO_INCREMENT NOT NULL, waterbody_id INT NOT NULL, toxicity_alert VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, source VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_17FD46C1BB7052F2 (waterbody_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C1BB7052F2 FOREIGN KEY (waterbody_id) REFERENCES waterbody (id)');
        $this->addSql('ALTER TABLE waterbody ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE waterbody ADD CONSTRAINT FK_576A1747C54C8C93 FOREIGN KEY (type_id) REFERENCES waterbody_type (id)');
        $this->addSql('CREATE INDEX IDX_576A1747C54C8C93 ON waterbody (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C1BB7052F2');
        $this->addSql('DROP TABLE alert');
        $this->addSql('ALTER TABLE waterbody DROP FOREIGN KEY FK_576A1747C54C8C93');
        $this->addSql('DROP INDEX IDX_576A1747C54C8C93 ON waterbody');
        $this->addSql('ALTER TABLE waterbody DROP type_id');
    }
}
