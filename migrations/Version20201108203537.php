<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108203537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE circuit_user (circuit_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E46BB0B4CF2182C8 (circuit_id), INDEX IDX_E46BB0B4A76ED395 (user_id), PRIMARY KEY(circuit_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE circuit_user ADD CONSTRAINT FK_E46BB0B4CF2182C8 FOREIGN KEY (circuit_id) REFERENCES circuit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE circuit_user ADD CONSTRAINT FK_E46BB0B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE run DROP circuits, DROP distances');
        $this->addSql('ALTER TABLE circuit ADD run_id INT DEFAULT NULL, ADD raw_name VARCHAR(255) NOT NULL, CHANGE distance distance DOUBLE PRECISION DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE group_size group_size INT DEFAULT NULL, CHANGE min_age min_age INT DEFAULT NULL, CHANGE max_age max_age INT DEFAULT NULL');
        $this->addSql('ALTER TABLE circuit ADD CONSTRAINT FK_1325F3A684E3FEC4 FOREIGN KEY (run_id) REFERENCES run (id)');
        $this->addSql('CREATE INDEX IDX_1325F3A684E3FEC4 ON circuit (run_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE circuit_user');
        $this->addSql('ALTER TABLE circuit DROP FOREIGN KEY FK_1325F3A684E3FEC4');
        $this->addSql('DROP INDEX IDX_1325F3A684E3FEC4 ON circuit');
        $this->addSql('ALTER TABLE circuit DROP run_id, DROP raw_name, CHANGE distance distance DOUBLE PRECISION NOT NULL, CHANGE type type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE group_size group_size INT NOT NULL, CHANGE min_age min_age INT NOT NULL, CHANGE max_age max_age INT NOT NULL');
        $this->addSql('ALTER TABLE run ADD circuits LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\', ADD distances LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\'');
    }
}
