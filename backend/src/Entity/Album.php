<?php

namespace App\Entity;

use App\Entity\Traits\HasUniqueId;
use App\Entity\Traits\TruncateStrings;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'albums')
]
class Album
{
    use HasUniqueId;
    use TruncateStrings;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'artist_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Artist $artist;

    #[ORM\Column(length: 255, nullable: false)]
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $this->truncateString($name);
    }

    public function __construct(
        Artist $artist
    ) {
        $this->artist = $artist;
    }
}
