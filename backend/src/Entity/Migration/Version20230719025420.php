<?php

declare(strict_types=1);

namespace App\Entity\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230719025420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Basic Schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE albums (id UUID NOT NULL, artist_id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_F4E2474FB7970CF8 ON albums (artist_id)');
        $this->addSql(
            'CREATE TABLE artists (id UUID NOT NULL, label_id UUID DEFAULT NULL, owner_id VARCHAR(150) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_68D3801E33B92F39 ON artists (label_id)');
        $this->addSql('CREATE INDEX IDX_68D3801E7E3C61F9 ON artists (owner_id)');
        $this->addSql(
            'CREATE TABLE labels (id UUID NOT NULL, owner_id VARCHAR(150) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_B5D102117E3C61F9 ON labels (owner_id)');
        $this->addSql(
            'CREATE TABLE tracks (id UUID NOT NULL, album_id UUID NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_246D2A2E1137ABCF ON tracks (album_id)');
        $this->addSql(
            'ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FB7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
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
            'ALTER TABLE tracks ADD CONSTRAINT FK_246D2A2E1137ABCF FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE albums DROP CONSTRAINT FK_F4E2474FB7970CF8');
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E33B92F39');
        $this->addSql('ALTER TABLE artists DROP CONSTRAINT FK_68D3801E7E3C61F9');
        $this->addSql('ALTER TABLE labels DROP CONSTRAINT FK_B5D102117E3C61F9');
        $this->addSql('ALTER TABLE tracks DROP CONSTRAINT FK_246D2A2E1137ABCF');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE labels');
        $this->addSql('DROP TABLE tracks');
    }
}
