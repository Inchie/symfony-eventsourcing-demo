<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201128142519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            '
                CREATE TABLE `comment` 
                    (
                        id CHAR(36) NOT NULL, 
                        blog_id CHAR(36) DEFAULT NULL, 
                        author VARCHAR(100) NOT NULL, 
                        comment LONGTEXT NOT NULL
                    )
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            '
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE comment');
    }
}
