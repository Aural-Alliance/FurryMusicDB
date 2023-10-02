<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231002161957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add permissions simple array to DB.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD permissions TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN users.permissions IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users DROP permissions');
    }
}
