<?php

namespace App\Entity;

use App\Entity\Traits\HasCommonRecordFields;
use Carbon\CarbonImmutable;
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

    public function __construct(
        ?Label $label = null,
        ?User $owner = null
    ) {
        $this->created_at = CarbonImmutable::now();
        $this->label = $label;
        $this->owner = $owner;
    }
}
