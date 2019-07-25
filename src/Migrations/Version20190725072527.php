<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190725072527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY stock_ibfk_1');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY stock_ibfk_2');
        $this->addSql('DROP INDEX article_id ON stock');
        $this->addSql('DROP INDEX insitute_id ON stock');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stock ADD CONSTRAINT stock_ibfk_1 FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT stock_ibfk_2 FOREIGN KEY (insitute_id) REFERENCES institute (id)');
        $this->addSql('CREATE INDEX article_id ON stock (article_id)');
        $this->addSql('CREATE INDEX insitute_id ON stock (insitute_id)');
    }
}
