<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201031175427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE race RENAME TO run');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE run RENAME TO race');
        // this down() migration is auto-generated, please modify it to your needs

    }
}
