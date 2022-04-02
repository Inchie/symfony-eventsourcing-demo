<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201122181953 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create Blog';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            '
                CREATE TABLE `blog`
                    (
                        id CHAR(36) NOT NULL, 
                        name VARCHAR(100) NOT NULL, 
                        author LONGTEXT NOT NULL
                    )
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            '
        );
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE blog');
    }

    public function isTransactional(): bool
    {
        return false;
    }

}
