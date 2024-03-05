<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240305022833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove Doctrine ORM 2 comments, update permissions to be nullable.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artists ALTER created_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE artists ALTER updated_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE artists ALTER art_updated_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN artists.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN artists.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN artists.art_updated_at IS \'\'');

        $this->addSql('ALTER TABLE labels ALTER created_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE labels ALTER updated_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE labels ALTER art_updated_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');

        $this->addSql('COMMENT ON COLUMN labels.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN labels.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN labels.art_updated_at IS \'\'');

        $this->addSql('ALTER TABLE users ALTER permissions TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN users.permissions IS \'\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ALTER permissions TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN users.permissions IS \'(DC2Type:simple_array)\'');

        $this->addSql('ALTER TABLE labels ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE labels ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE labels ALTER art_updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN labels.created_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN labels.updated_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN labels.art_updated_at IS \'(DC2Type:carbon_immutable)\'');

        $this->addSql('ALTER TABLE artists ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE artists ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE artists ALTER art_updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN artists.created_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN artists.updated_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN artists.art_updated_at IS \'(DC2Type:carbon_immutable)\'');
    }
}
