<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250621084946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__event AS SELECT id, client_name, event_name, event_date, created_at, updated_at, user_id, google_calendar_event_id, start_time, end_time, is_full_day FROM event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, event_name VARCHAR(255) NOT NULL, event_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INTEGER NOT NULL, google_calendar_event_id VARCHAR(255) DEFAULT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, is_full_day BOOLEAN DEFAULT 0 NOT NULL, CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO event (id, client_name, event_name, event_date, created_at, updated_at, user_id, google_calendar_event_id, start_time, end_time, is_full_day) SELECT id, client_name, event_name, event_date, created_at, updated_at, user_id, google_calendar_event_id, start_time, end_time, is_full_day FROM __temp__event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__event
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3BAE0AA7A76ED395 ON event (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__event AS SELECT id, client_name, event_name, event_date, start_time, end_time, is_full_day, google_calendar_event_id, created_at, updated_at, user_id FROM event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, event_name VARCHAR(255) NOT NULL, event_date DATETIME NOT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, is_full_day BOOLEAN DEFAULT 0 NOT NULL, google_calendar_event_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO event (id, client_name, event_name, event_date, start_time, end_time, is_full_day, google_calendar_event_id, created_at, updated_at, user_id) SELECT id, client_name, event_name, event_date, start_time, end_time, is_full_day, google_calendar_event_id, created_at, updated_at, user_id FROM __temp__event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__event
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3BAE0AA7A76ED395 ON event (user_id)
        SQL);
    }
}
