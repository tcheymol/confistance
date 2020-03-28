<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200328171826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE game_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE app_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE squad_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE squad_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE game (id INT NOT NULL, creator_id INT NOT NULL, is_over BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, index_next_player INT DEFAULT NULL, index_next_squad INT DEFAULT NULL, is_started BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_232B318C61220EA6 ON game (creator_id)');
        $this->addSql('CREATE TABLE game_participant (id INT NOT NULL, game_id INT NOT NULL, app_user_id INT DEFAULT NULL, is_spy BOOLEAN DEFAULT NULL, spot_index INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CA2913E48FD905 ON game_participant (game_id)');
        $this->addSql('CREATE INDEX IDX_9CA29134A3353D8 ON game_participant (app_user_id)');
        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE TABLE squad_member (id INT NOT NULL, squad_id INT NOT NULL, member_id INT DEFAULT NULL, spy_played BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CCFAFA7DF1B2C7C ON squad_member (squad_id)');
        $this->addSql('CREATE INDEX IDX_CCFAFA77597D3FE ON squad_member (member_id)');
        $this->addSql('CREATE TABLE squad (id INT NOT NULL, game_id INT NOT NULL, status VARCHAR(255) NOT NULL, spots_count INT DEFAULT NULL, squad_index INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CFD0FFE7E48FD905 ON squad (game_id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C61220EA6 FOREIGN KEY (creator_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_participant ADD CONSTRAINT FK_9CA2913E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_participant ADD CONSTRAINT FK_9CA29134A3353D8 FOREIGN KEY (app_user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE squad_member ADD CONSTRAINT FK_CCFAFA7DF1B2C7C FOREIGN KEY (squad_id) REFERENCES squad (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE squad_member ADD CONSTRAINT FK_CCFAFA77597D3FE FOREIGN KEY (member_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE squad ADD CONSTRAINT FK_CFD0FFE7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE game_participant DROP CONSTRAINT FK_9CA2913E48FD905');
        $this->addSql('ALTER TABLE squad DROP CONSTRAINT FK_CFD0FFE7E48FD905');
        $this->addSql('ALTER TABLE game DROP CONSTRAINT FK_232B318C61220EA6');
        $this->addSql('ALTER TABLE game_participant DROP CONSTRAINT FK_9CA29134A3353D8');
        $this->addSql('ALTER TABLE squad_member DROP CONSTRAINT FK_CCFAFA77597D3FE');
        $this->addSql('ALTER TABLE squad_member DROP CONSTRAINT FK_CCFAFA7DF1B2C7C');
        $this->addSql('DROP SEQUENCE game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_participant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE app_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE squad_member_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE squad_id_seq CASCADE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_participant');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE squad_member');
        $this->addSql('DROP TABLE squad');
    }
}
