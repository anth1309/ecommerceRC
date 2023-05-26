<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523155410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders ADD is_paid TINYINT(1) DEFAULT NULL, ADD srtipe_session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE reset_token reset_token VARCHAR(128) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP is_paid, DROP srtipe_session_id');
        $this->addSql('ALTER TABLE users CHANGE reset_token reset_token VARCHAR(128) DEFAULT NULL');
    }
}
