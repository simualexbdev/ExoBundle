<?php

namespace UJM\ExoBundle\Migrations\pdo_mysql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/08/27 03:28:39
 */
class Version20150827152836 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE ujm_type_timedQcm (
                id INT AUTO_INCREMENT NOT NULL, 
                value VARCHAR(255) NOT NULL, 
                code INT NOT NULL, 
                UNIQUE INDEX UNIQ_8B6461CD77153098 (code), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE ujm_interaction_timedQcm (
                id INT AUTO_INCREMENT NOT NULL, 
                interaction_id INT DEFAULT NULL, 
                limited_time TINYINT(1) DEFAULT NULL, 
                duration TIME DEFAULT NULL, 
                shuffle TINYINT(1) DEFAULT NULL, 
                score_right_response DOUBLE PRECISION DEFAULT NULL, 
                score_false_response DOUBLE PRECISION DEFAULT NULL, 
                weight_response TINYINT(1) DEFAULT NULL, 
                html_course_complement LONGTEXT DEFAULT NULL, 
                html_course_complement_duration TIME DEFAULT NULL, 
                type_timedQcm_id INT DEFAULT NULL, 
                UNIQUE INDEX UNIQ_62CF5F90886DEE8F (interaction_id), 
                INDEX IDX_62CF5F9040452D5F (type_timedQcm_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            CREATE TABLE ujm_timedQcm_choice (
                id INT AUTO_INCREMENT NOT NULL, 
                `label` LONGTEXT NOT NULL, 
                ordre INT NOT NULL, 
                weight DOUBLE PRECISION DEFAULT NULL, 
                feedback LONGTEXT DEFAULT NULL, 
                right_response TINYINT(1) DEFAULT NULL, 
                position_force TINYINT(1) DEFAULT NULL, 
                interaction_timedQcm_id INT DEFAULT NULL, 
                INDEX IDX_2440BF4842731066 (interaction_timedQcm_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");
        $this->addSql("
            ALTER TABLE ujm_interaction_timedQcm 
            ADD CONSTRAINT FK_62CF5F90886DEE8F FOREIGN KEY (interaction_id) 
            REFERENCES ujm_interaction (id)
        ");
        $this->addSql("
            ALTER TABLE ujm_interaction_timedQcm 
            ADD CONSTRAINT FK_62CF5F9040452D5F FOREIGN KEY (type_timedQcm_id) 
            REFERENCES ujm_type_timedQcm (id)
        ");
        $this->addSql("
            ALTER TABLE ujm_timedQcm_choice 
            ADD CONSTRAINT FK_2440BF4842731066 FOREIGN KEY (interaction_timedQcm_id) 
            REFERENCES ujm_interaction_timedQcm (id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE ujm_interaction_timedQcm 
            DROP FOREIGN KEY FK_62CF5F9040452D5F
        ");
        $this->addSql("
            ALTER TABLE ujm_timedQcm_choice 
            DROP FOREIGN KEY FK_2440BF4842731066
        ");
        $this->addSql("
            DROP TABLE ujm_type_timedQcm
        ");
        $this->addSql("
            DROP TABLE ujm_interaction_timedQcm
        ");
        $this->addSql("
            DROP TABLE ujm_timedQcm_choice
        ");
    }
}