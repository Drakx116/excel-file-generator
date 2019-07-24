<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190724145345 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE institute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, reason VARCHAR(35) NOT NULL, receiver VARCHAR(35) NOT NULL, address1 VARCHAR(35) NOT NULL, address2 VARCHAR(35) NOT NULL, address3 VARCHAR(35) NOT NULL, post_code INT NOT NULL, city VARCHAR(25) NOT NULL, phone_number VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE insitute');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE insitute (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci, reason VARCHAR(35) NOT NULL COLLATE utf8mb4_unicode_ci, receiver VARCHAR(35) NOT NULL COLLATE utf8mb4_unicode_ci, address1 VARCHAR(35) NOT NULL COLLATE utf8mb4_unicode_ci, address2 VARCHAR(35) NOT NULL COLLATE utf8mb4_unicode_ci, address3 VARCHAR(35) NOT NULL COLLATE utf8mb4_unicode_ci, post_code INT NOT NULL, city VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, phone_number VARCHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE institute');
    }
}
