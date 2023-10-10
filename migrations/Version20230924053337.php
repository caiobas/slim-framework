<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230924053337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Users table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE users (
                id SERIAL NOT NULL, 
                email VARCHAR(255) NOT NULL,
                password VARCHAR(127) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP, 
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP, 
                deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX idx__user_email ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX idx__user_id ON users (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx__user_id');
        $this->addSql('DROP INDEX idx__user_email');
        $this->addSql('DROP TABLE users');
    }
}
