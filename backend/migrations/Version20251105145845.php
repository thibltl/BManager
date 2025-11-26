<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251105145845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE users_users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE projects_projects_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE priority_priority_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tasks_tasks_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_history_th_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE priority_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE projects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tasks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE th_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE th (id INT NOT NULL, th_changelog VARCHAR(255) NOT NULL, th_update DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE participants DROP CONSTRAINT participants_projects_id_fkey');
        $this->addSql('ALTER TABLE participants DROP CONSTRAINT participants_users_id_fkey');
        $this->addSql('ALTER TABLE task_history DROP CONSTRAINT task_history_task_id_fkey');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE task_history');
        $this->addSql('ALTER TABLE priority DROP CONSTRAINT priority_pkey');
        $this->addSql('ALTER TABLE priority ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE priority DROP priority_id');
        $this->addSql('ALTER TABLE priority ALTER priority_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE priority ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE projects DROP CONSTRAINT projects_pkey');
        $this->addSql('ALTER TABLE projects ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE projects DROP projects_id');
        $this->addSql('ALTER TABLE projects ALTER projects_description TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE projects ALTER projects_description SET NOT NULL');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat TYPE DATE');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat DROP DEFAULT');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat SET NOT NULL');
        $this->addSql('ALTER TABLE projects ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE status DROP CONSTRAINT status_pkey');
        $this->addSql('ALTER TABLE status ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE status DROP status_id');
        $this->addSql('ALTER TABLE status ALTER status_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE status ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT tasks_priority_id_fkey');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT tasks_projects_id_fkey');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT tasks_status_id_fkey');
        $this->addSql('DROP INDEX IDX_50586597497B19F9');
        $this->addSql('DROP INDEX IDX_505865971EDE0F55');
        $this->addSql('DROP INDEX IDX_505865976BF700BD');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT tasks_pkey');
        $this->addSql('ALTER TABLE tasks ADD tasks_priority_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD tasks_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD tasks_desciption VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD tasks_laschange DATE NOT NULL');
        $this->addSql('ALTER TABLE tasks DROP tasks_id');
        $this->addSql('ALTER TABLE tasks DROP priority_id');
        $this->addSql('ALTER TABLE tasks DROP status_id');
        $this->addSql('ALTER TABLE tasks DROP tasks_description');
        $this->addSql('ALTER TABLE tasks DROP tasks_lastchange');
        $this->addSql('ALTER TABLE tasks ALTER tasks_duedate TYPE DATE');
        $this->addSql('ALTER TABLE tasks ALTER tasks_duedate SET NOT NULL');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat TYPE DATE');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat DROP DEFAULT');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat SET NOT NULL');
        $this->addSql('ALTER TABLE tasks RENAME COLUMN projects_id TO id');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597F6937D24 FOREIGN KEY (tasks_priority_id) REFERENCES priority (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597B6FEF71F FOREIGN KEY (tasks_status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50586597F6937D24 ON tasks (tasks_priority_id)');
        $this->addSql('CREATE INDEX IDX_50586597B6FEF71F ON tasks (tasks_status_id)');
        $this->addSql('ALTER TABLE tasks ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX users_users_email_key');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT users_pkey');
        $this->addSql('ALTER TABLE users ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE users ALTER users_id DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER users_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE users ALTER users_password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE users ALTER users_createdat TYPE DATE');
        $this->addSql('ALTER TABLE users ALTER users_createdat DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER users_createdat SET NOT NULL');
        $this->addSql('ALTER TABLE users ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE priority_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE projects_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tasks_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE th_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE users_users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE projects_projects_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE priority_priority_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tasks_tasks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_history_th_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE participants (users_id INT NOT NULL, projects_id INT NOT NULL, role_in_project VARCHAR(100) DEFAULT NULL, PRIMARY KEY(users_id, projects_id))');
        $this->addSql('CREATE INDEX IDX_716970921EDE0F55 ON participants (projects_id)');
        $this->addSql('CREATE INDEX IDX_7169709267B3B43D ON participants (users_id)');
        $this->addSql('CREATE TABLE task_history (th_id SERIAL NOT NULL, task_id INT NOT NULL, th_changelog TEXT NOT NULL, th_updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(th_id))');
        $this->addSql('CREATE INDEX IDX_385B5AA18DB60186 ON task_history (task_id)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT participants_projects_id_fkey FOREIGN KEY (projects_id) REFERENCES projects (projects_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT participants_users_id_fkey FOREIGN KEY (users_id) REFERENCES users (users_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_history ADD CONSTRAINT task_history_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks (tasks_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE th');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX users_pkey');
        $this->addSql('ALTER TABLE users DROP id');
        $this->addSql('CREATE SEQUENCE users_users_id_seq');
        $this->addSql('SELECT setval(\'users_users_id_seq\', (SELECT MAX(users_id) FROM users))');
        $this->addSql('ALTER TABLE users ALTER users_id SET DEFAULT nextval(\'users_users_id_seq\')');
        $this->addSql('ALTER TABLE users ALTER users_name TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE users ALTER users_password TYPE TEXT');
        $this->addSql('ALTER TABLE users ALTER users_createdat TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE users ALTER users_createdat SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE users ALTER users_createdat DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX users_users_email_key ON users (users_email)');
        $this->addSql('ALTER TABLE users ADD PRIMARY KEY (users_id)');
        $this->addSql('DROP INDEX projects_pkey');
        $this->addSql('ALTER TABLE projects ADD projects_id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE projects DROP id');
        $this->addSql('ALTER TABLE projects ALTER projects_description TYPE TEXT');
        $this->addSql('ALTER TABLE projects ALTER projects_description DROP NOT NULL');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE projects ALTER projects_createdat DROP NOT NULL');
        $this->addSql('ALTER TABLE projects ADD PRIMARY KEY (projects_id)');
        $this->addSql('DROP INDEX priority_pkey');
        $this->addSql('ALTER TABLE priority ADD priority_id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE priority DROP id');
        $this->addSql('ALTER TABLE priority ALTER priority_name TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE priority ADD PRIMARY KEY (priority_id)');
        $this->addSql('DROP INDEX status_pkey');
        $this->addSql('ALTER TABLE status ADD status_id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE status DROP id');
        $this->addSql('ALTER TABLE status ALTER status_name TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE status ADD PRIMARY KEY (status_id)');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597F6937D24');
        $this->addSql('ALTER TABLE tasks DROP CONSTRAINT FK_50586597B6FEF71F');
        $this->addSql('DROP INDEX IDX_50586597F6937D24');
        $this->addSql('DROP INDEX IDX_50586597B6FEF71F');
        $this->addSql('DROP INDEX tasks_pkey');
        $this->addSql('ALTER TABLE tasks ADD tasks_id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD priority_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD tasks_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks ADD tasks_lastchange TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tasks DROP tasks_priority_id');
        $this->addSql('ALTER TABLE tasks DROP tasks_status_id');
        $this->addSql('ALTER TABLE tasks DROP tasks_desciption');
        $this->addSql('ALTER TABLE tasks DROP tasks_laschange');
        $this->addSql('ALTER TABLE tasks ALTER tasks_duedate TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE tasks ALTER tasks_duedate DROP NOT NULL');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat SET DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE tasks ALTER tasks_createdat DROP NOT NULL');
        $this->addSql('ALTER TABLE tasks RENAME COLUMN id TO projects_id');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT tasks_priority_id_fkey FOREIGN KEY (priority_id) REFERENCES priority (priority_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT tasks_projects_id_fkey FOREIGN KEY (projects_id) REFERENCES projects (projects_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT tasks_status_id_fkey FOREIGN KEY (status_id) REFERENCES status (status_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_50586597497B19F9 ON tasks (priority_id)');
        $this->addSql('CREATE INDEX IDX_505865971EDE0F55 ON tasks (projects_id)');
        $this->addSql('CREATE INDEX IDX_505865976BF700BD ON tasks (status_id)');
        $this->addSql('ALTER TABLE tasks ADD PRIMARY KEY (tasks_id)');
    }
}
