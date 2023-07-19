<?php

namespace App\Entity;

use App\Entity\Traits\HasUniqueId;
use App\Entity\Traits\TruncateStrings;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'tracks')
]
class Track
{
    use HasUniqueId;
    use TruncateStrings;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'album_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected Album $album;

    #[ORM\Column(length: 255, nullable: false)]
    protected string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $this->truncateString($title);
    }

    public function __construct(
        Album $album
    ) {
        $this->album = $album;
    }
}
