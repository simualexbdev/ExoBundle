<?php

namespace UJM\ExoBundle\Migrations\drizzle_pdo_mysql;

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
            ALTER TABLE ujm_question CHANGE title title VARCHAR(255) DEFAULT NULL, 
            CHANGE description description TEXT DEFAULT NULL
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE ujm_question CHANGE title title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, 
            CHANGE description description TEXT NOT NULL COLLATE utf8_unicode_ci
        ");
    }
}