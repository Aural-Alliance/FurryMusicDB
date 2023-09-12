<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230911234624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add art_updated_at.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artists ADD art_updated_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN artists.art_updated_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('ALTER TABLE labels ADD art_updated_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN labels.art_updated_at IS \'(DC2Type:carbon_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artists DROP art_updated_at');
        $this->addSql('ALTER TABLE labels DROP art_updated_at');
    }
}
