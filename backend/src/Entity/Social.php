<?php

namespace App\Entity;

use App\Entity\Enums\SocialTypes;
use App\Entity\Traits\HasUniqueId;
use App\Entity\Traits\TruncateStrings;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity,
    ORM\Table(name: 'socials')
]
class Social
{
    use HasUniqueId;
    use TruncateStrings;

    #[ORM\ManyToOne(inversedBy: 'socials')]
    #[ORM\JoinColumn(name: 'label_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Label $label = null;

    #[ORM\ManyToOne(inversedBy: 'socials')]
    #[ORM\JoinColumn(name: 'artist_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Artist $artist = null;

    #[
        ORM\Column(type: 'string', length: 150, enumType: SocialTypes::class)
    ]
    protected SocialTypes $type;

    public function getType(): SocialTypes
    {
        return $this->type;
    }

    public function setType(SocialTypes $type): void
    {
        $this->type = $type;
    }

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $this->truncateNullableString($name);
    }

    #[ORM\Column(length: 255, nullable: false)]
    protected string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $this->truncateString($value);
    }

    public function __construct(
        Label|Artist $parent
    ) {
        if ($parent instanceof Label) {
            $this->label = $parent;
        } else {
            $this->artist = $parent;
        }
    }
}
