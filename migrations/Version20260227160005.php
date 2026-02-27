<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227160005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE picture ADD alert_id INT DEFAULT NULL');
        $this->addSql('UPDATE picture p SET p.alert_id = (SELECT a.id FROM alert a WHERE a.waterbody_id = p.waterbody_id ORDER BY a.created_at DESC LIMIT 1) WHERE p.alert_id IS NULL');
        $this->addSql('DELETE FROM picture WHERE alert_id IS NULL');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89BB7052F2');
        $this->addSql('DROP INDEX IDX_16DB4F89BB7052F2 ON picture');
        $this->addSql('ALTER TABLE picture DROP waterbody_id');
        $this->addSql('ALTER TABLE picture MODIFY alert_id INT NOT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8993035F72 FOREIGN KEY (alert_id) REFERENCES alert (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F8993035F72 ON picture (alert_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8993035F72');
        $this->addSql('DROP INDEX IDX_16DB4F8993035F72 ON picture');
        $this->addSql('ALTER TABLE picture ADD waterbody_id INT DEFAULT NULL, DROP alert_id');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89BB7052F2 FOREIGN KEY (waterbody_id) REFERENCES waterbody (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_16DB4F89BB7052F2 ON picture (waterbody_id)');
    }
}
