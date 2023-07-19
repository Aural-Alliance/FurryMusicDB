<?php

namespace App\Entity;

use App\Entity\Traits\HasUniqueId;
use App\Entity\Traits\TruncateStrings;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'artists')
]
class Artist
{
    use HasUniqueId;
    use TruncateStrings;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'label_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Label $label = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?User $owner = null;

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
        ?Label $label = null,
        ?User $owner = null
    ) {
        $this->label = $label;
        $this->owner = $owner;
    }
}
