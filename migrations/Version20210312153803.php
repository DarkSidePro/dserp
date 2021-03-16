<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210312153803 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3C7BE036FC');
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3CECC6147F');
        $this->addSql('DROP INDEX IDX_C7783A3CECC6147F ON component_operation');
        $this->addSql('DROP INDEX IDX_C7783A3C7BE036FC ON component_operation');
        $this->addSql('ALTER TABLE component_operation DROP production_id, DROP shipment_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation ADD production_id INT DEFAULT NULL, ADD shipment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3C7BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3CECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('CREATE INDEX IDX_C7783A3CECC6147F ON component_operation (production_id)');
        $this->addSql('CREATE INDEX IDX_C7783A3C7BE036FC ON component_operation (shipment_id)');
    }
}
