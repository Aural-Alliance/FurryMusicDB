<?php

namespace App\Entity;

use App\Auth\Permissions;
use Doctrine\DBAL\Types\Types;
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
        if (null !== $this->avatar) {
            return $this->avatar;
        }

        $urlParams = [
            'd' => 'mm',
            'r' => 'g',
            'size' => 128,
        ];

        $avatarUrl = 'https://www.gravatar.com/avatar/' . md5(strtolower($this->getEmail() ?? ''))
            . '?' . http_build_query($urlParams);
        return htmlspecialchars($avatarUrl, ENT_QUOTES | ENT_HTML5);
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @var Permissions[]
     */
    #[
        ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true, enumType: Permissions::class)
    ]
    protected array $permissions = [];

    /**
     * @return Permissions[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param Permissions[] $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
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
        $this->permissions = Permissions::authenticated();
    }
}
