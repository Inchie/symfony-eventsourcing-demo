<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201121170017 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            '
                CREATE TABLE user 
                    (
                        id INT AUTO_INCREMENT NOT NULL, 
                        name VARCHAR(100) NOT NULL, 
                        mail LONGTEXT NOT NULL, 
                        stream LONGTEXT NOT NULL, 
                        PRIMARY KEY(id)
                    ) 
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            '
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE user');
    }
}
