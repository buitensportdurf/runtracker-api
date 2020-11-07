<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201107214713 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE circuit (id INT AUTO_INCREMENT NOT NULL, distance DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, group_size INT NOT NULL, min_age INT NOT NULL, max_age INT NOT NULL, description VARCHAR(255) DEFAULT NULL, price INT NOT NULL, points INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, ADD gender VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE circuit');
        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, DROP gender, DROP city');
    }
}
