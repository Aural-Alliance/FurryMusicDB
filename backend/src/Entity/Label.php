<?php

namespace App\Entity;

use App\Entity\Traits\HasCommonRecordFields;
use App\Normalizer\Attributes\DeepNormalize;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'labels'),
    ORM\HasLifecycleCallbacks
]
class Label
{
    use HasCommonRecordFields;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?User $owner = null;

    /** @var Collection<array-key, Social> */
    #[ORM\OneToMany(targetEntity: Social::class, mappedBy: 'label')]
    #[DeepNormalize(true)]
    protected Collection $socials;

    public function getSocials(): Collection
    {
        return $this->socials;
    }

    public function __construct(
        ?User $owner = null
    ) {
        $this->created_at = time();
        $this->owner = $owner;
    }
}
