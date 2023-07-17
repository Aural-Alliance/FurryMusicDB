<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity,
    ORM\Table(name: 'users')
]
class User
{
    #[
        ORM\Column(type: 'string', length: 150, nullable: false),
        ORM\Id,
        ORM\GeneratedValue('NONE'),
    ]
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }

    #[
        ORM\Column(length: 100, nullable: true),
        Assert\Email,
    ]
    protected ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    #[
        ORM\Column(length: 255, nullable: true)
    ]
    protected ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    #[
        ORM\Column(length: 255, nullable: true)
    ]
    protected ?string $avatar = null;

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    #[
        ORM\Column
    ]
    protected int $updatedAt = 0;

    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    public function updated(): void
    {
        $this->updatedAt = time();
    }

    public function __construct(
        string $id
    ) {
        $this->id = $id;
    }
}
