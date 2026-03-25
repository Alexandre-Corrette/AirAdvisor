<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325141338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review ADD COLUMN is_visible BOOLEAN NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, rating SMALLINT NOT NULL, rating_comfort SMALLINT DEFAULT NULL, rating_punctuality SMALLINT DEFAULT NULL, rating_staff SMALLINT DEFAULT NULL, rating_food SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, flight_id INTEGER NOT NULL, CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id) SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('CREATE INDEX IDX_794381C691F478C5 ON review (flight_id)');
    }
}
