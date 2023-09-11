<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230911033633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema V2';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE artists (id UUID NOT NULL, label_id UUID DEFAULT NULL, owner_id VARCHAR(150) DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, description TEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_68D3801E33B92F39 ON artists (label_id)');
        $this->addSql('CREATE INDEX IDX_68D3801E7E3C61F9 ON artists (owner_id)');
        $this->addSql('COMMENT ON COLUMN artists.created_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN artists.updated_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql(
            'CREATE TABLE labels (id UUID NOT NULL, owner_id VARCHAR(150) DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, description TEXT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B5D102117E3C61F9 ON labels (owner_id)');
        $this->addSql('COMMENT ON COLUMN labels.created_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql('COMMENT ON COLUMN labels.updated_at IS \'(DC2Type:carbon_immutable)\'');
        $this->addSql(
            'ALTER TABLE artists ADD CONSTRAINT FK_68D3801E33B92F39 FOREIGN KEY (label_id) REFERENCES labels (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE artists ADD CONSTRAINT FK_68D3801E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE labels ADD CONSTRAINT FK_B5D102117E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E33B92F39');
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E7E3C61F9');
        $this->addSql('ALTER TABLE labels DROP CONSTRAINT FK_B5D102117E3C61F9');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE labels');
    }
}
