<?php

namespace App\Entity;

use App\Repository\RunRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: RunRepository::class)]
#[ORM\Table(name: 'run')]
class Run
{
    public const CIRCUIT_LONG = 'long';
    public const CIRCUIT_MEDIUM = 'medium';
    public const CIRCUIT_SHORT = 'short';
    public const CIRCUIT_YOUTH = 'youth';

    #[Groups(['from_run'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[Groups(['from_run'])]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[Groups(['from_run'])]
    #[ORM\Column]
    private ?string $city = null;

    #[Groups(['from_run'])]
    #[ORM\Column]
    private int $age = 0;

    #[Groups(['from_run'])]
    #[ORM\ManyToOne]
    private ?Organization $organization = null;

    #[Groups(['from_run'])]
    #[ORM\Column]
    private bool $cancelled = false;

    /** subscribe url */
    #[Groups(['from_run'])]
    #[ORM\Column(nullable: true)]
    private ?string $subscribe = null;

    /** results url */
    #[Groups(['from_run'])]
    #[ORM\Column(nullable: true)]
    private ?string $result = null;

    /**
     * @var ArrayCollection<Circuit>
     */
    #[Groups(['from_run'])]
    #[ORM\OneToMany(mappedBy: 'run', targetEntity: Circuit::class)]
    private ArrayCollection $circuits;

    #[Groups(['from_run'])]
    #[ORM\Column(nullable: true)]
    private ?int $enrollId = null;

    #[Groups(['from_run'])]
    #[ORM\Column(nullable: true)]
    private ?DateTime $opensAt = null;

    public function __construct()
    {
        $this->circuits = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s in %s', $this->date->format('Y-m-d'), $this->city);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['from_run'])]
    #[SerializedName('type')]
    public function getObjectType(): string
    {
        return 'run';
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * @param bool $cancelled
     *
     * @return Run
     */
    public function setCancelled(bool $cancelled): Run
    {
        $this->cancelled = $cancelled;
        return $this;
    }

    public function getSubscribe(): bool
    {
        return $this->subscribe;
    }

    public function setSubscribe(string $subscribe): self
    {
        $this->subscribe = $subscribe;
        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return ArrayCollection<Circuit>
     */
    public function getCircuits(): Collection
    {
        return $this->circuits;
    }

    public function getEnrollId(): ?int
    {
        return $this->enrollId;
    }

    public function setEnrollId(?int $enrollId): Run
    {
        $this->enrollId = $enrollId;
        return $this;
    }

    public function getOpensAt(): ?DateTime
    {
        return $this->opensAt;
    }

    public function setOpensAt(?DateTime $opensAt): Run
    {
        $this->opensAt = $opensAt;
        return $this;
    }
}
