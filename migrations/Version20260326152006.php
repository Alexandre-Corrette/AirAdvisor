<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326152006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__airline AS SELECT id, name, iata_code, logo_url, country, slug, is_verified FROM airline');
        $this->addSql('DROP TABLE airline');
        $this->addSql('CREATE TABLE airline (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iata_code VARCHAR(10) NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO airline (id, name, iata_code, logo_url, country, slug, is_verified) SELECT id, name, iata_code, logo_url, country, slug, is_verified FROM __temp__airline');
        $this->addSql('DROP TABLE __temp__airline');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF8989D9B62 ON airline (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF83D151C2D ON airline (iata_code)');
        $this->addSql('ALTER TABLE airline_account ADD COLUMN stripe_account_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__airline AS SELECT id, name, iata_code, logo_url, country, slug, is_verified FROM airline');
        $this->addSql('DROP TABLE airline');
        $this->addSql('CREATE TABLE airline (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iata_code VARCHAR(10) NOT NULL, logo_url VARCHAR(255) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_verified BOOLEAN DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO airline (id, name, iata_code, logo_url, country, slug, is_verified) SELECT id, name, iata_code, logo_url, country, slug, is_verified FROM __temp__airline');
        $this->addSql('DROP TABLE __temp__airline');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF83D151C2D ON airline (iata_code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EC141EF8989D9B62 ON airline (slug)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__airline_account AS SELECT id, email, password, company_name, phone, is_verified, created_at, airline_id FROM airline_account');
        $this->addSql('DROP TABLE airline_account');
        $this->addSql('CREATE TABLE airline_account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, phone VARCHAR(30) DEFAULT NULL, is_verified BOOLEAN NOT NULL, created_at DATETIME NOT NULL, airline_id INTEGER DEFAULT NULL, CONSTRAINT FK_677235E6130D0C16 FOREIGN KEY (airline_id) REFERENCES airline (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO airline_account (id, email, password, company_name, phone, is_verified, created_at, airline_id) SELECT id, email, password, company_name, phone, is_verified, created_at, airline_id FROM __temp__airline_account');
        $this->addSql('DROP TABLE __temp__airline_account');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_677235E6E7927C74 ON airline_account (email)');
        $this->addSql('CREATE INDEX IDX_677235E6130D0C16 ON airline_account (airline_id)');
    }
}
