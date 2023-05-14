<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230513181600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD total INT DEFAULT NULL, CHANGE reference reference VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE reset_token reset_token VARCHAR(128) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP total, CHANGE reference reference VARCHAR(64) NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE reset_token reset_token VARCHAR(128) DEFAULT NULL');
    }
}
