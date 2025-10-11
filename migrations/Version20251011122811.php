<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251011122811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE toxicity_level (id INT AUTO_INCREMENT NOT NULL, level VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert ADD toxicity_level_id INT NOT NULL, DROP toxicity_alert');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C157BBE1BD FOREIGN KEY (toxicity_level_id) REFERENCES toxicity_level (id)');
        $this->addSql('CREATE INDEX IDX_17FD46C157BBE1BD ON alert (toxicity_level_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C157BBE1BD');
        $this->addSql('DROP TABLE toxicity_level');
        $this->addSql('DROP INDEX IDX_17FD46C157BBE1BD ON alert');
        $this->addSql('ALTER TABLE alert ADD toxicity_alert VARCHAR(50) NOT NULL, DROP toxicity_level_id');
    }
}
