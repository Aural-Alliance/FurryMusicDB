<?php

namespace App\Entity;

use App\Normalizer\Attributes\DeepNormalize;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'labels'),
    ORM\HasLifecycleCallbacks
]
class Label extends AbstractListing
{
    /** @var Collection<array-key, Social> */
    #[ORM\OneToMany(targetEntity: Social::class, mappedBy: 'label')]
    #[DeepNormalize(true)]
    protected Collection $socials;

    public function getSocials(): Collection
    {
        return $this->socials;
    }
}
