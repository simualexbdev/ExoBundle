<?php

namespace UJM\ExoBundle\Migrations\sqlanywhere;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2015/03/20 02:22:33
 */
class Version20150320142231 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE ujm_question ALTER title VARCHAR(255) DEFAULT NULL, 
            ALTER description TEXT DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE ujm_question ALTER title VARCHAR(255) NOT NULL, 
            ALTER description TEXT NOT NULL
        ");
    }
}