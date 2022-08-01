<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220709155800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(/** @lang MySQL */
            "
            CREATE TABLE `symfony_demo_events` (
              `sequencenumber` int NOT NULL,
              `stream` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `version` bigint UNSIGNED NOT NULL,
              `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `metadata` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `correlationidentifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `causationidentifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `recordedat` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            "
        );

        $this->addSql(/** @lang MySQL */
            "
            ALTER TABLE `symfony_demo_events`
              ADD PRIMARY KEY (`sequencenumber`),
              ADD UNIQUE KEY `id_uniq` (`id`),
              ADD UNIQUE KEY `stream_version_uniq` (`stream`,`version`),
              ADD KEY `IDX_78B75FE39EE6C504` (`correlationidentifier`);
            "
        );

        $this->addSql(/** @lang MySQL */
            '
            ALTER TABLE `symfony_demo_events`
                MODIFY `sequencenumber` int NOT NULL AUTO_INCREMENT;
            '
        );

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
