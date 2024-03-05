<?php

namespace App\Entity;

use App\Entity\Traits\HasCommonRecordFields;
use App\Normalizer\Attributes\DeepNormalize;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'artists'),
    ORM\HasLifecycleCallbacks
]
class Artist
{
    use HasCommonRecordFields;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'label_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Label $label = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?User $owner = null;

    /** @var Collection<array-key, Social> */
    #[ORM\OneToMany(targetEntity: Social::class, mappedBy: 'artist')]
    #[DeepNormalize(true)]
    protected Collection $socials;

    public function getSocials(): Collection
    {
        return $this->socials;
    }

    public function __construct(
        ?Label $label = null,
        ?User $owner = null
    ) {
        $this->created_at = time();
        $this->label = $label;
        $this->owner = $owner;
    }
}
