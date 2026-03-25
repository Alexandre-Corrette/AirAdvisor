<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325100518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE airline (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iata_code VARCHAR(10) NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, slug VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF83D151C2D ON airline (iata_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF8989D9B62 ON airline (slug)');
        $this->addSql('CREATE TABLE flight (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, flight_number VARCHAR(20) NOT NULL, flight_iata_code VARCHAR(20) DEFAULT NULL, departure_city VARCHAR(255) NOT NULL, departure_iata_code VARCHAR(10) DEFAULT NULL, arrival_city VARCHAR(255) NOT NULL, arrival_iata_code VARCHAR(10) DEFAULT NULL, flight_date DATE NOT NULL, scheduled_departure_time DATETIME DEFAULT NULL, scheduled_arrival_time DATETIME DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, airline_id INTEGER DEFAULT NULL, CONSTRAINT FK_C257E60E130D0C16 FOREIGN KEY (airline_id) REFERENCES airline (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C257E60E130D0C16 ON flight (airline_id)');
        $this->addSql('CREATE TABLE flight_passengers (flight_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (flight_id, user_id), CONSTRAINT FK_C626443791F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6264437A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C626443791F478C5 ON flight_passengers (flight_id)');
        $this->addSql('CREATE INDEX IDX_C6264437A76ED395 ON flight_passengers (user_id)');
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content CLOB NOT NULL, rating SMALLINT NOT NULL, rating_comfort SMALLINT DEFAULT NULL, rating_punctuality SMALLINT DEFAULT NULL, rating_staff SMALLINT DEFAULT NULL, rating_food SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, flight_id INTEGER NOT NULL, CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C691F478C5 FOREIGN KEY (flight_id) REFERENCES flight (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_794381C6F675F31B ON review (author_id)');
        $this->addSql('CREATE INDEX IDX_794381C691F478C5 ON review (flight_id)');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(55) DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, departure_city VARCHAR(100) DEFAULT NULL, roles CLOB NOT NULL, created_at DATETIME NOT NULL, profile_slug VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE airline');
        $this->addSql('DROP TABLE flight');
        $this->addSql('DROP TABLE flight_passengers');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
