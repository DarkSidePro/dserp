<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313165257 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation CHANGE `out` dispatch NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_operation CHANGE `exit` dispatch NUMERIC(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_operation CHANGE dispatch `out` NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_operation CHANGE dispatch `exit` NUMERIC(10, 2) DEFAULT NULL');
    }
}
