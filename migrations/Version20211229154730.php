<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211229154730 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight ADD status VARCHAR(255) DEFAULT NULL, ADD scheduled_time DATETIME DEFAULT NULL, ADD departure_iata_code VARCHAR(255) DEFAULT NULL, ADD arrival_iata_code VARCHAR(255) DEFAULT NULL, ADD arrival_scheduled_time DATETIME DEFAULT NULL, ADD flight_iata_code VARCHAR(255) DEFAULT NULL, ADD codeshared_airline_name VARCHAR(255) DEFAULT NULL, ADD codeshared_airline_iata_code VARCHAR(255) DEFAULT NULL, ADD airline_name VARCHAR(255) DEFAULT NULL, ADD airline_iata_code VARCHAR(255) DEFAULT NULL, ADD codeshared_flight_number INT DEFAULT NULL, ADD codeshared_flight_iata_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flight DROP status, DROP scheduled_time, DROP departure_iata_code, DROP arrival_iata_code, DROP arrival_scheduled_time, DROP flight_iata_code, DROP codeshared_airline_name, DROP codeshared_airline_iata_code, DROP airline_name, DROP airline_iata_code, DROP codeshared_flight_number, DROP codeshared_flight_iata_number');
    }
}
