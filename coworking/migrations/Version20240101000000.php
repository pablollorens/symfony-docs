<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema: user, room, booking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE room (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, capacity INT NOT NULL, equipment JSON NOT NULL, hourly_rate NUMERIC(8, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE booking (id SERIAL NOT NULL, room_id INT NOT NULL, user_id INT NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total_cost NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDE54177093 ON booking (room_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEA76ED395 ON booking (user_id)');
        $this->addSql('COMMENT ON COLUMN booking.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE54177093');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDEA76ED395');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE "user"');
    }
}
