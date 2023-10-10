<?php

declare(strict_types=1);

namespace migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230924215310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Stock Quotes table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE stock_quotes (
                id SERIAL NOT NULL, 
                user_id int NOT NULL,
                name VARCHAR(50) NOT NULL,
                symbol VARCHAR(100) NOT NULL,
                open VARCHAR(100) NOT NULL,
                high VARCHAR(100) NOT NULL,                
                low VARCHAR(100) NOT NULL,
                close VARCHAR(100) NOT NULL,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP, 
                deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX idx__stock_quote_id ON stock_quotes (id)');
        $this->addSql('ALTER TABLE stock_quotes ADD CONSTRAINT FK_STOCKQUOTEUSER FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx__stock_quote_id');
        $this->addSql('DROP TABLE stock_quotes');
    }
}
