<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017122058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    // public function up(Schema $schema): void
    // {
    //     // this up() migration is auto-generated, please modify it to your needs
    //     $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, venue VARCHAR(255) NOT NULL, capacity INT NOT NULL, INDEX IDX_3BAE0AA7876C4DDA (organizer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    //     $this->addSql('CREATE TABLE organizer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    //     $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, event_id INT NOT NULL, seat_number VARCHAR(3) NOT NULL, price DOUBLE PRECISION NOT NULL, status VARCHAR(40) NOT NULL, INDEX IDX_97A0ADA371F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    //     $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES organizer (id)');
    //     $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    // }

    

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE organizers (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )');

        $this->addSql('CREATE TABLE events (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            date DATETIME NOT NULL,
            venue VARCHAR(255) NOT NULL,
            capacity INT NOT NULL,
            organizer_id INT NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_EVENTS_ORGANIZER FOREIGN KEY (organizer_id) REFERENCES organizers (id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE TABLE tickets (
            id INT AUTO_INCREMENT NOT NULL,
            seat_number VARCHAR(50) NOT NULL,
            price FLOAT NOT NULL,
            status VARCHAR(20) NOT NULL,
            event_id INT NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_TICKETS_EVENT FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
        )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7876C4DDA');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA371F7E88B');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE organizer');
        $this->addSql('DROP TABLE ticket');
    }

}
