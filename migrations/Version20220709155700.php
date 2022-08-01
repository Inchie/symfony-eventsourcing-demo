<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220709155700 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $sql = /** @lang MySQL */ "
            
            CREATE TABLE `neos_eventsourcing_eventlistener_appliedeventslog`(
                `subscriberid` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `appliedsequencenumber` INT NOT NULL
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

            INSERT INTO `neos_eventsourcing_eventlistener_appliedeventslog`(
                `subscriberid`,
                `appliedsequencenumber`
            )
            VALUES
                ('Blog', 0),
                ('User', 0),
                ('Comment', 0);

            ALTER TABLE `neos_eventsourcing_eventlistener_appliedeventslog`
                ADD PRIMARY KEY (`subscriberid`);
        ";

        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        #$this->addSql('DROP TABLE comment');
    }

    public function isTransactional(): bool
    {
        return false;
    }

}
