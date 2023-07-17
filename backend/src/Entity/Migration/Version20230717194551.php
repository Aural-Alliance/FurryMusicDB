<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230717194551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User DB setup.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE users (id VARCHAR(150) NOT NULL, email VARCHAR(100) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, updatedAt INT NOT NULL, PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE users');
    }
}
