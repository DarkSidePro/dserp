<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210316123118 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E07BE036FC');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E0ECC6147F');
        $this->addSql('DROP INDEX IDX_772110E07BE036FC ON product_operation');
        $this->addSql('DROP INDEX IDX_772110E0ECC6147F ON product_operation');
        $this->addSql('ALTER TABLE product_operation ADD production_id_id INT DEFAULT NULL, ADD shipment_id_id INT DEFAULT NULL, DROP shipment_id, DROP production_id');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E065455A61 FOREIGN KEY (production_id_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E0F3893CF2 FOREIGN KEY (shipment_id_id) REFERENCES shipment (id)');
        $this->addSql('CREATE INDEX IDX_772110E065455A61 ON product_operation (production_id_id)');
        $this->addSql('CREATE INDEX IDX_772110E0F3893CF2 ON product_operation (shipment_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E065455A61');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E0F3893CF2');
        $this->addSql('DROP INDEX IDX_772110E065455A61 ON product_operation');
        $this->addSql('DROP INDEX IDX_772110E0F3893CF2 ON product_operation');
        $this->addSql('ALTER TABLE product_operation ADD shipment_id INT DEFAULT NULL, ADD production_id INT DEFAULT NULL, DROP production_id_id, DROP shipment_id_id');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E07BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E0ECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('CREATE INDEX IDX_772110E07BE036FC ON product_operation (shipment_id)');
        $this->addSql('CREATE INDEX IDX_772110E0ECC6147F ON product_operation (production_id)');
    }
}
