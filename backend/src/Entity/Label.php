<?php

namespace App\Entity;

use App\Entity\Traits\HasUniqueId;
use App\Entity\Traits\TruncateStrings;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'labels')
]
class Label
{
    use HasUniqueId;
    use TruncateStrings;

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
        ?User $owner = null
    ) {
        $this->owner = $owner;
    }
}
