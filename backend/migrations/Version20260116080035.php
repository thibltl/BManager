<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260116080035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_members (project_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY (project_id, user_id))');
        $this->addSql('CREATE INDEX IDX_D3BEDE9A166D1F9C ON project_members (project_id)');
        $this->addSql('CREATE INDEX IDX_D3BEDE9AA76ED395 ON project_members (user_id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_project DROP CONSTRAINT fk_77becee4166d1f9c');
        $this->addSql('ALTER TABLE user_project DROP CONSTRAINT fk_77becee4a76ed395');
        $this->addSql('DROP TABLE user_project');
        $this->addSql('ALTER TABLE tasks ALTER position DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_project (user_id INT NOT NULL, project_id INT NOT NULL, PRIMARY KEY (user_id, project_id))');
        $this->addSql('CREATE INDEX idx_77becee4a76ed395 ON user_project (user_id)');
        $this->addSql('CREATE INDEX idx_77becee4166d1f9c ON user_project (project_id)');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT fk_77becee4166d1f9c FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_project ADD CONSTRAINT fk_77becee4a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_members DROP CONSTRAINT FK_D3BEDE9A166D1F9C');
        $this->addSql('ALTER TABLE project_members DROP CONSTRAINT FK_D3BEDE9AA76ED395');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('ALTER TABLE tasks ALTER "position" SET DEFAULT 0');
    }
}
