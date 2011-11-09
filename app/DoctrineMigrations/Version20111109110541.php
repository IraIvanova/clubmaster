<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20111109110541 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE club_team_schedule DROP FOREIGN KEY FK_5FBD8B31A40BC2D5");
        $this->addSql("ALTER TABLE club_team_schedule ADD CONSTRAINT FK_5FBD8B31A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES club_team_schedule(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE club_team_repetition DROP FOREIGN KEY FK_BC76ED49A40BC2D5");
        $this->addSql("ALTER TABLE club_team_repetition ADD CONSTRAINT FK_BC76ED49A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES club_team_schedule(id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE club_team_repetition DROP FOREIGN KEY FK_BC76ED49A40BC2D5");
        $this->addSql("ALTER TABLE club_team_repetition ADD CONSTRAINT FK_BC76ED49A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES club_team_schedule(id)");
        $this->addSql("ALTER TABLE club_team_schedule DROP FOREIGN KEY FK_5FBD8B31A40BC2D5");
        $this->addSql("ALTER TABLE club_team_schedule ADD CONSTRAINT FK_5FBD8B31A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES club_team_schedule(id)");
    }
}
