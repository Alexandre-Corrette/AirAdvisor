<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326145210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE airline_account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, phone VARCHAR(30) DEFAULT NULL, is_verified BOOLEAN NOT NULL, created_at DATETIME NOT NULL, airline_id INTEGER DEFAULT NULL, CONSTRAINT FK_677235E6130D0C16 FOREIGN KEY (airline_id) REFERENCES airline (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_677235E6E7927C74 ON airline_account (email)');
        $this->addSql('CREATE INDEX IDX_677235E6130D0C16 ON airline_account (airline_id)');
        $this->addSql('CREATE TABLE airline_claim (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(30) DEFAULT NULL, message CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, reviewed_at DATETIME DEFAULT NULL, airline_id INTEGER NOT NULL, CONSTRAINT FK_74C36920130D0C16 FOREIGN KEY (airline_id) REFERENCES airline (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_74C36920130D0C16 ON airline_claim (airline_id)');
        $this->addSql('ALTER TABLE airline ADD COLUMN is_verified BOOLEAN NOT NULL DEFAULT 0');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id, is_visible FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, rating SMALLINT NOT NULL, rating_comfort SMALLINT DEFAULT NULL, rating_punctuality SMALLINT DEFAULT NULL, rating_staff SMALLINT DEFAULT NULL, rating_food SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, flight_id INTEGER NOT NULL, is_visible BOOLEAN NOT NULL, CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id, is_visible) SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, created_at, author_id, flight_id, is_visible FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C691F478C5 ON review (flight_id)');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE airline_account');
        $this->addSql('DROP TABLE airline_claim');
        $this->addSql('CREATE TEMPORARY TABLE __temp__airline AS SELECT id, name, iata_code, logo_url, country, slug FROM airline');
        $this->addSql('DROP TABLE airline');
        $this->addSql('CREATE TABLE airline (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iata_code VARCHAR(10) NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, slug VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO airline (id, name, iata_code, logo_url, country, slug) SELECT id, name, iata_code, logo_url, country, slug FROM __temp__airline');
        $this->addSql('DROP TABLE __temp__airline');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF83D151C2D ON airline (iata_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF8989D9B62 ON airline (slug)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__review AS SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, is_visible, created_at, author_id, flight_id FROM review');
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, rating SMALLINT NOT NULL, rating_comfort SMALLINT DEFAULT NULL, rating_punctuality SMALLINT DEFAULT NULL, rating_staff SMALLINT DEFAULT NULL, rating_food SMALLINT DEFAULT NULL, is_visible BOOLEAN DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, flight_id INTEGER NOT NULL, CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO review (id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, is_visible, created_at, author_id, flight_id) SELECT id, title, content, rating, rating_comfort, rating_punctuality, rating_staff, rating_food, is_visible, created_at, author_id, flight_id FROM __temp__review');
        $this->addSql('DROP TABLE __temp__review');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('CREATE INDEX IDX_794381C691F478C5 ON review (flight_id)');
    }
}
