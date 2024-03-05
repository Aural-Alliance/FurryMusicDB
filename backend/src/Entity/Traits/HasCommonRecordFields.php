<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasCommonRecordFields
{
    use HasUniqueId;
    use TruncateStrings;

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

    #[ORM\Column]
    protected int $created_at;

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    #[ORM\Column]
    protected int $updated_at;

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    #[
        ORM\PrePersist,
        ORM\PreUpdate
    ]
    public function updated(): void
    {
        $this->updated_at = time();
    }

    #[ORM\Column(nullable: true)]
    protected ?int $art_updated_at = null;

    public function getArtUpdatedAt(): ?int
    {
        return $this->art_updated_at;
    }

    public function setArtUpdated(): void
    {
        $this->art_updated_at = time();
    }

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $url;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function __construct()
    {
        $this->created_at = time();
    }
}
