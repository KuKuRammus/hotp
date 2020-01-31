<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200131221849 extends AbstractMigration {
    public function getDescription(): string
    {
        return 'Create protected_message table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE protected_message (
                id INT GENERATED ALWAYS AS IDENTITY,
                name VARCHAR(255) NOT NULL,
                secret VARCHAR(255) NOT NULL,
                content VARCHAR(255) NOT NULL,
                
                PRIMARY KEY (id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE protected_message
        ');
    }
}
