<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210320152700 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, animal_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE component (id INT AUTO_INCREMENT NOT NULL, component_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE component_operation (id INT AUTO_INCREMENT NOT NULL, component_id INT NOT NULL, production_id_id INT DEFAULT NULL, shipment_id_id INT DEFAULT NULL, enter NUMERIC(10, 2) DEFAULT NULL, dispatch NUMERIC(10, 2) DEFAULT NULL, modification NUMERIC(10, 2) DEFAULT NULL, production NUMERIC(10, 2) DEFAULT NULL, shipment NUMERIC(10, 2) DEFAULT NULL, state NUMERIC(10, 2) NOT NULL, datestamp DATETIME NOT NULL, INDEX IDX_C7783A3CE2ABAFFF (component_id), INDEX IDX_C7783A3C65455A61 (production_id_id), INDEX IDX_C7783A3CF3893CF2 (shipment_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, animal_id INT NOT NULL, product_name VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD8E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_operation (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, production_id_id INT DEFAULT NULL, shipment_id_id INT DEFAULT NULL, enter NUMERIC(10, 2) DEFAULT NULL, dispatch NUMERIC(10, 2) DEFAULT NULL, modification NUMERIC(10, 2) DEFAULT NULL, production NUMERIC(10, 2) DEFAULT NULL, state NUMERIC(10, 2) NOT NULL, datestamp DATETIME NOT NULL, shipment NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_772110E04584665A (product_id), INDEX IDX_772110E065455A61 (production_id_id), INDEX IDX_772110E0F3893CF2 (shipment_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, recipe_id INT NOT NULL, modification TINYINT(1) NOT NULL, datestamp DATETIME NOT NULL, reference VARCHAR(50) DEFAULT NULL, INDEX IDX_D3EDB1E04584665A (product_id), INDEX IDX_D3EDB1E059D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_detail (id INT AUTO_INCREMENT NOT NULL, production_id INT NOT NULL, component_id INT NOT NULL, value NUMERIC(10, 2) NOT NULL, INDEX IDX_E1A6EBCEECC6147F (production_id), INDEX IDX_E1A6EBCEE2ABAFFF (component_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, recipe_name VARCHAR(255) NOT NULL, INDEX IDX_DA88B1374584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_detail (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, component_id INT NOT NULL, amount NUMERIC(8, 2) NOT NULL, INDEX IDX_A31861B259D8A214 (recipe_id), INDEX IDX_A31861B2E2ABAFFF (component_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment (id INT AUTO_INCREMENT NOT NULL, datestamp DATETIME NOT NULL, reference VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_client (id INT AUTO_INCREMENT NOT NULL, shipment_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_61749E907BE036FC (shipment_id), INDEX IDX_61749E9019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipment_client_detail (id INT AUTO_INCREMENT NOT NULL, shipment_client_id INT NOT NULL, product_id INT NOT NULL, value NUMERIC(10, 2) NOT NULL, INDEX IDX_90594F7623F86F10 (shipment_client_id), INDEX IDX_90594F764584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, email VARCHAR(250) NOT NULL, password VARCHAR(4000) NOT NULL, roles JSON NOT NULL, registred DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3CE2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3C65455A61 FOREIGN KEY (production_id_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE component_operation ADD CONSTRAINT FK_C7783A3CF3893CF2 FOREIGN KEY (shipment_id_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E065455A61 FOREIGN KEY (production_id_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE product_operation ADD CONSTRAINT FK_772110E0F3893CF2 FOREIGN KEY (shipment_id_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE production_detail ADD CONSTRAINT FK_E1A6EBCEECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE production_detail ADD CONSTRAINT FK_E1A6EBCEE2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1374584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE recipe_detail ADD CONSTRAINT FK_A31861B259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_detail ADD CONSTRAINT FK_A31861B2E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shipment_client ADD CONSTRAINT FK_61749E907BE036FC FOREIGN KEY (shipment_id) REFERENCES shipment (id)');
        $this->addSql('ALTER TABLE shipment_client ADD CONSTRAINT FK_61749E9019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE shipment_client_detail ADD CONSTRAINT FK_90594F7623F86F10 FOREIGN KEY (shipment_client_id) REFERENCES shipment_client (id)');
        $this->addSql('ALTER TABLE shipment_client_detail ADD CONSTRAINT FK_90594F764584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8E962C16');
        $this->addSql('ALTER TABLE shipment_client DROP FOREIGN KEY FK_61749E9019EB6921');
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3CE2ABAFFF');
        $this->addSql('ALTER TABLE production_detail DROP FOREIGN KEY FK_E1A6EBCEE2ABAFFF');
        $this->addSql('ALTER TABLE recipe_detail DROP FOREIGN KEY FK_A31861B2E2ABAFFF');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E04584665A');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E04584665A');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1374584665A');
        $this->addSql('ALTER TABLE shipment_client_detail DROP FOREIGN KEY FK_90594F764584665A');
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3C65455A61');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E065455A61');
        $this->addSql('ALTER TABLE production_detail DROP FOREIGN KEY FK_E1A6EBCEECC6147F');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E059D8A214');
        $this->addSql('ALTER TABLE recipe_detail DROP FOREIGN KEY FK_A31861B259D8A214');
        $this->addSql('ALTER TABLE component_operation DROP FOREIGN KEY FK_C7783A3CF3893CF2');
        $this->addSql('ALTER TABLE product_operation DROP FOREIGN KEY FK_772110E0F3893CF2');
        $this->addSql('ALTER TABLE shipment_client DROP FOREIGN KEY FK_61749E907BE036FC');
        $this->addSql('ALTER TABLE shipment_client_detail DROP FOREIGN KEY FK_90594F7623F86F10');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE component');
        $this->addSql('DROP TABLE component_operation');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_operation');
        $this->addSql('DROP TABLE production');
        $this->addSql('DROP TABLE production_detail');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_detail');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE shipment');
        $this->addSql('DROP TABLE shipment_client');
        $this->addSql('DROP TABLE shipment_client_detail');
        $this->addSql('DROP TABLE user');
    }
}
