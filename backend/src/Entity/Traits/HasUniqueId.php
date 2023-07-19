<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Doctrine\Generator\UuidV6Generator;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;

trait HasUniqueId
{
    #[
        ORM\Column(type: 'guid', unique: true, nullable: false),
        ORM\Id, ORM\GeneratedValue(strategy: 'CUSTOM'), ORM\CustomIdGenerator(UuidV6Generator::class),
    ]
    protected ?string $id = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIdRequired(): string
    {
        if (null === $this->id) {
            throw new RuntimeException('An ID was not generated for this object.');
        }

        return $this->id;
    }
}
