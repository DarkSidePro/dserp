<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507152905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment_client_detail DROP FOREIGN KEY FK_90594F764584665A');
        $this->addSql('DROP INDEX IDX_90594F764584665A ON shipment_client_detail');
        $this->addSql('ALTER TABLE shipment_client_detail CHANGE product_id product_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE shipment_client_detail ADD CONSTRAINT FK_90594F76DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_90594F76DE18E50B ON shipment_client_detail (product_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipment_client_detail DROP FOREIGN KEY FK_90594F76DE18E50B');
        $this->addSql('DROP INDEX IDX_90594F76DE18E50B ON shipment_client_detail');
        $this->addSql('ALTER TABLE shipment_client_detail CHANGE product_id_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE shipment_client_detail ADD CONSTRAINT FK_90594F764584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_90594F764584665A ON shipment_client_detail (product_id)');
    }
}
