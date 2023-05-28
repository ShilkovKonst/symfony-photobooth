<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525180353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD mob_tel VARCHAR(255) NOT NULL, ADD is_email_verified TINYINT(1) NOT NULL, ADD avatar VARCHAR(255) DEFAULT NULL, ADD zip_code VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD street VARCHAR(255) NOT NULL, ADD build_number VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP first_name, DROP last_name, DROP mob_tel, DROP is_email_verified, DROP avatar, DROP zip_code, DROP city, DROP street, DROP build_number');
    }
}
