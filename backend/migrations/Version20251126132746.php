<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126132746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tasks ADD task_priority_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD task_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597F0EB4CB FOREIGN KEY (task_priority_id) REFERENCES priority (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659714DDCDEC FOREIGN KEY (task_status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50586597F0EB4CB ON tasks (task_priority_id)');
        $this->addSql('CREATE INDEX IDX_5058659714DDCDEC ON tasks (task_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597F0EB4CB');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_5058659714DDCDEC');
        $this->addSql('DROP INDEX IDX_50586597F0EB4CB');
        $this->addSql('DROP INDEX IDX_5058659714DDCDEC');
        $this->addSql('ALTER TABLE tasks DROP task_priority_id');
        $this->addSql('ALTER TABLE tasks DROP task_status_id');
    }
}
