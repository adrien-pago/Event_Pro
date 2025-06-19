<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250619115059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "client" (id CHAR(36) NOT NULL --(DC2Type:guid)
            , first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(5) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, wedding_date VARCHAR(255) DEFAULT NULL, wedding_location VARCHAR(255) DEFAULT NULL, notes CLOB DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "devis" (id CHAR(36) NOT NULL --(DC2Type:guid)
            , client_id CHAR(36) NOT NULL --(DC2Type:guid)
            , reference VARCHAR(255) NOT NULL, date DATETIME NOT NULL, notes CLOB DEFAULT NULL, total_amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES "client" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8B27C52B19EB6921 ON "devis" (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "devis_prestation" (id CHAR(36) NOT NULL --(DC2Type:guid)
            , devis_id CHAR(36) NOT NULL --(DC2Type:guid)
            , prestation_id CHAR(36) NOT NULL --(DC2Type:guid)
            , quantity DOUBLE PRECISION NOT NULL, unit_price DOUBLE PRECISION NOT NULL, total_price DOUBLE PRECISION NOT NULL, notes CLOB DEFAULT NULL, is_discounted BOOLEAN NOT NULL, discount_rate DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_E169C44541DEFADA FOREIGN KEY (devis_id) REFERENCES "devis" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E169C4459E45C554 FOREIGN KEY (prestation_id) REFERENCES "prestation" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E169C44541DEFADA ON "devis_prestation" (devis_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E169C4459E45C554 ON "devis_prestation" (prestation_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "prestation" (id CHAR(36) NOT NULL --(DC2Type:guid)
            , name VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, details CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX email ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX username ON "user" (username)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE "client"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "devis"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "devis_prestation"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "prestation"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
