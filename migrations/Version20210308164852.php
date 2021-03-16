<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210308164852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe_detail (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, component_id INT NOT NULL, amount NUMERIC(8, 2) NOT NULL, INDEX IDX_A31861B259D8A214 (recipe_id), INDEX IDX_A31861B2E2ABAFFF (component_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_detail ADD CONSTRAINT FK_A31861B259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_detail ADD CONSTRAINT FK_A31861B2E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE product_operation ADD shipment_id INT DEFAULT NULL, ADD production_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E07BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E0ECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('CREATE INDEX IDX_772110E07BE036FC ON product_operation (shipment_id)');
        $this->addSql('CREATE INDEX IDX_772110E0ECC6147F ON product_operation (production_id)');
        $this->addSql('ALTER TABLE user CHANGE firstname firstname VARCHAR(50) NOT NULL, CHANGE lastname lastname VARCHAR(50) NOT NULL, CHANGE email email VARCHAR(250) NOT NULL, CHANGE password password VARCHAR(4000) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE recipe_detail');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E07BE036FC');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E0ECC6147F');
        $this->addSql('DROP INDEX IDX_772110E07BE036FC ON product_operation');
        $this->addSql('DROP INDEX IDX_772110E0ECC6147F ON product_operation');
        $this->addSql('ALTER TABLE product_operation DROP shipment_id, DROP production_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user CHANGE firstname firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE lastname lastname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(4096) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
