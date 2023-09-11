<?php

namespace App\Entity;

use App\Entity\Traits\HasCommonRecordFields;
use Carbon\CarbonImmutable;
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

    public function __construct(
        ?User $owner = null
    ) {
        $this->created_at = CarbonImmutable::now();
        $this->owner = $owner;
    }
}
