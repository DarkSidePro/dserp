<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210321164728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3C41BA078F');
        $this->addSql('DROP INDEX UNIQ_C7783A3C41BA078F ON component_operation');
        $this->addSql('ALTER TABLE component_operation CHANGE production_detail_id_id production_detail_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3C7F4F5261 FOREIGN KEY (production_detail_id) REFERENCES production_detail (id)');
        $this->addSql('CREATE INDEX IDX_C7783A3C7F4F5261 ON component_operation (production_detail_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3C7F4F5261');
        $this->addSql('DROP INDEX IDX_C7783A3C7F4F5261 ON component_operation');
        $this->addSql('ALTER TABLE component_operation CHANGE production_detail_id production_detail_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3C41BA078F FOREIGN KEY (production_detail_id_id) REFERENCES production_detail (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7783A3C41BA078F ON component_operation (production_detail_id_id)');
    }
}
