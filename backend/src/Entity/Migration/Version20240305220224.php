<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240305220224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial database schema setup.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE artists (name VARCHAR(255) NOT NULL, created_at INT NOT NULL, updated_at INT NOT NULL, art_updated_at INT DEFAULT NULL, description TEXT DEFAULT NULL, id UUID NOT NULL, label_id UUID DEFAULT NULL, owner_id VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_68D3801E33B92F39 ON artists (label_id)');
        $this->addSql('CREATE INDEX IDX_68D3801E7E3C61F9 ON artists (owner_id)');

        $this->addSql(
            'CREATE TABLE labels (name VARCHAR(255) NOT NULL, created_at INT NOT NULL, updated_at INT NOT NULL, art_updated_at INT DEFAULT NULL, description TEXT DEFAULT NULL, id UUID NOT NULL, owner_id VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B5D102117E3C61F9 ON labels (owner_id)');

        $this->addSql(
            'CREATE TABLE socials (type VARCHAR(150) NOT NULL, name VARCHAR(255) DEFAULT NULL, value VARCHAR(255) NOT NULL, id UUID NOT NULL, label_id UUID DEFAULT NULL, artist_id UUID DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_68A3B86933B92F39 ON socials (label_id)');
        $this->addSql('CREATE INDEX IDX_68A3B869B7970CF8 ON socials (artist_id)');

        $this->addSql(
            'CREATE TABLE users (id VARCHAR(150) NOT NULL, email VARCHAR(100) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, permissions TEXT DEFAULT NULL, updatedAt INT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'ALTER TABLE artists ADD CONSTRAINT FK_68D3801E33B92F39 FOREIGN KEY (label_id) REFERENCES labels (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE artists ADD CONSTRAINT FK_68D3801E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE labels ADD CONSTRAINT FK_B5D102117E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE socials ADD CONSTRAINT FK_68A3B86933B92F39 FOREIGN KEY (label_id) REFERENCES labels (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE socials ADD CONSTRAINT FK_68A3B869B7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E33B92F39');
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E7E3C61F9');
        $this->addSql('ALTER TABLE labels DROP CONSTRAINT FK_B5D102117E3C61F9');
        $this->addSql('ALTER TABLE socials DROP CONSTRAINT FK_68A3B86933B92F39');
        $this->addSql('ALTER TABLE socials DROP CONSTRAINT FK_68A3B869B7970CF8');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE labels');
        $this->addSql('DROP TABLE socials');
        $this->addSql('DROP TABLE users');
    }
}
