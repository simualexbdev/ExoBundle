<?php

namespace UJM\ExoBundle\Migrations\pdo_sqlite;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/03/03 01:32:22
 */
class Version20150303133220 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE ujm_category 
            ADD COLUMN locker BOOLEAN NOT NULL
        ");
        $this->addSql("
            DROP INDEX IDX_B797C100FAB79C10
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__ujm_proposal AS 
            SELECT id, 
            interaction_matching_id, 
            value 
            FROM ujm_proposal
        ");
        $this->addSql("
            DROP TABLE ujm_proposal
        ");
        $this->addSql("
            CREATE TABLE ujm_proposal (
                id INTEGER NOT NULL, 
                interaction_matching_id INTEGER DEFAULT NULL, 
                value CLOB NOT NULL COLLATE utf8_unicode_ci, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_B797C100FAB79C10 FOREIGN KEY (interaction_matching_id) 
                REFERENCES ujm_interaction_matching (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO ujm_proposal (
                id, interaction_matching_id, value
            ) 
            SELECT id, 
            interaction_matching_id, 
            value 
            FROM __temp__ujm_proposal
        ");
        $this->addSql("
            DROP TABLE __temp__ujm_proposal
        ");
        $this->addSql("
            CREATE INDEX IDX_2672B44BFAB79C10 ON ujm_proposal (interaction_matching_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP INDEX IDX_9FDB39F8A76ED395
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__ujm_category AS 
            SELECT id, 
            user_id, 
            value 
            FROM ujm_category
        ");
        $this->addSql("
            DROP TABLE ujm_category
        ");
        $this->addSql("
            CREATE TABLE ujm_category (
                id INTEGER NOT NULL, 
                user_id INTEGER DEFAULT NULL, 
                value VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_9FDB39F8A76ED395 FOREIGN KEY (user_id) 
                REFERENCES claro_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO ujm_category (id, user_id, value) 
            SELECT id, 
            user_id, 
            value 
            FROM __temp__ujm_category
        ");
        $this->addSql("
            DROP TABLE __temp__ujm_category
        ");
        $this->addSql("
            CREATE INDEX IDX_9FDB39F8A76ED395 ON ujm_category (user_id)
        ");
        $this->addSql("
            DROP INDEX IDX_2672B44BFAB79C10
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__ujm_proposal AS 
            SELECT id, 
            interaction_matching_id, 
            value 
            FROM ujm_proposal
        ");
        $this->addSql("
            DROP TABLE ujm_proposal
        ");
        $this->addSql("
            CREATE TABLE ujm_proposal (
                id INTEGER NOT NULL, 
                interaction_matching_id INTEGER DEFAULT NULL, 
                value CLOB NOT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_2672B44BFAB79C10 FOREIGN KEY (interaction_matching_id) 
                REFERENCES ujm_interaction_matching (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO ujm_proposal (
                id, interaction_matching_id, value
            ) 
            SELECT id, 
            interaction_matching_id, 
            value 
            FROM __temp__ujm_proposal
        ");
        $this->addSql("
            DROP TABLE __temp__ujm_proposal
        ");
        $this->addSql("
            CREATE INDEX IDX_B797C100FAB79C10 ON ujm_proposal (interaction_matching_id)
        ");
    }
}