<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Carbon\CarbonImmutable;
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

    #[ORM\Column(type: 'carbon_immutable')]
    protected CarbonImmutable $created_at;

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    #[ORM\Column(type: 'carbon_immutable')]
    protected CarbonImmutable $updated_at;

    public function getUpdatedAt(): CarbonImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    public function updated(): void
    {
        $this->updated_at = CarbonImmutable::now();
    }

    #[ORM\Column(type: 'carbon_immutable', nullable: true)]
    protected ?CarbonImmutable $art_updated_at = null;

    public function getArtUpdatedAt(): ?CarbonImmutable
    {
        return $this->art_updated_at;
    }

    public function setArtUpdated(): void
    {
        $this->art_updated_at = CarbonImmutable::now();
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
        $this->created_at = CarbonImmutable::now();
    }
}
