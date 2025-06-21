<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620123859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE prestation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prix NUMERIC(10, 2) NOT NULL, marge NUMERIC(10, 2) NOT NULL, duree VARCHAR(255) NOT NULL, event_id INT NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_51C88FAD71F7E88B FOREIGN KEY (event_id) REFERENCES event (id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_51C88FAD71F7E88B ON prestation (event_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE prestation
        SQL);
    }
}
