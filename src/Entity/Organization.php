<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity]
#[ORM\Table(name: 'organization')]
class Organization
{
    #[Groups(['from_run'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['from_run'])]
    #[ORM\Column(unique: true)]
    private ?string $name = null;

    #[Groups(['from_run'])]
    #[ORM\Column]
    private ?string $website = null;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: Run::class)]
    private Collection $runs;

    public function __construct()
    {
        $this->runs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['from_run'])]
    #[SerializedName('type')]
    public function getObjectType(): string
    {
        return 'organization';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite($website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getRuns(): Collection
    {
        return $this->runs;
    }
}