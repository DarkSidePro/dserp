<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210312155712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation ADD production_id_id INT DEFAULT NULL, ADD shipment_id_id INT DEFAULT NULL, DROP production_id, DROP shipment_id');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3C65455A61 FOREIGN KEY (production_id_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3CF3893CF2 FOREIGN KEY (shipment_id_id) REFERENCES shipment (id)');
        $this->addSql('CREATE INDEX IDX_C7783A3C65455A61 ON component_operation (production_id_id)');
        $this->addSql('CREATE INDEX IDX_C7783A3CF3893CF2 ON component_operation (shipment_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3C65455A61');
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3CF3893CF2');
        $this->addSql('DROP INDEX IDX_C7783A3C65455A61 ON component_operation');
        $this->addSql('DROP INDEX IDX_C7783A3CF3893CF2 ON component_operation');
        $this->addSql('ALTER TABLE component_operation ADD production_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipment_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP production_id_id, DROP shipment_id_id');
    }
}
