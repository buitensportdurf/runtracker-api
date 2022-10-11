<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity]
#[ORM\Table(name: 'circuit')]
class Circuit
{
    const TYPE_COMPETITION_LONG = 'long';
    const TYPE_COMPETITION_MEDIUM = 'medium';
    const TYPE_COMPETITION_SHORT = 'short';
    const TYPE_COMPETITION_YOUTH = 'youth';
    const TYPE_COMPETITION_BASE = 'base';
    /**
     * @var integer
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Run
     */
    #[Groups(['from_circuit'])]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Run', inversedBy: 'circuits')]
    private $run;

    /**
     * @var User[]
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\ManyToMany(targetEntity: 'App\Entity\User')]
    private $users = [];

    /**
     * @var integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer')]
    private $userCount = 0;

    /**
     * @var integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer')]
    private $userCapacity = -1;

    /**
     * @var string
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'string')]
    private $rawName;

    /**
     * @var ?float
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'float', nullable: true)]
    private $distance;

    /**
     * @var ?string
     */
    #[Groups(['from_run'])]
    #[SerializedName('circuitType')]
    #[ORM\Column(type: 'string', nullable: true)]
    private $type;

    /**
     * @var ?string
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $competitionType;

    /**
     * @var ?integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $groupSize;

    /**
     * @var ?integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $minAge;

    /**
     * @var ?integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $maxAge;

    /**
     * @var ?string
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $description;

    /**
     * @var ?integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer')]
    private $price;

    /**
     * @var ?integer
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    private $points;

    /**
     * @var boolean
     */
    #[Groups(['from_run'])]
    #[ORM\Column(type: 'boolean')]
    private $dummy = false;

    public function __toString()
    {
        return $this->rawName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    #[Groups(['from_run'])]
    #[SerializedName('type')]
    public function getObjectType(): string
    {
        return 'circuit';
    }

    /**
     * @return Run
     */
    public function getRun(): Run
    {
        return $this->run;
    }

    /**
     * @param Run $run
     *
     * @return Circuit
     */
    public function setRun(Run $run): Circuit
    {
        $this->run = $run;
        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     *
     * @return Circuit
     */
    public function setUsers(array $users): Circuit
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user): Circuit
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getUserCount(): int
    {
        return $this->userCount;
    }

    /**
     * @param int $userCount
     *
     * @return Circuit
     */
    public function setUserCount(int $userCount): Circuit
    {
        $this->userCount = $userCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserCapacity(): int
    {
        return $this->userCapacity;
    }

    /**
     * @param int $userCapacity
     *
     * @return Circuit
     */
    public function setUserCapacity(int $userCapacity): Circuit
    {
        $this->userCapacity = $userCapacity;
        return $this;
    }

    /**
     * @return string
     */
    public function getRawName(): string
    {
        return $this->rawName;
    }

    /**
     * @param string $rawName
     *
     * @return Circuit
     */
    public function setRawName(string $rawName): Circuit
    {
        $this->rawName = $rawName;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistance(): ?float
    {
        return $this->distance;
    }

    /**
     * @param float|null $distance
     *
     * @return Circuit
     */
    public function setDistance(?float $distance): Circuit
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return Circuit
     */
    public function setType(?string $type): Circuit
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompetitionType(): ?string
    {
        return $this->competitionType;
    }

    /**
     * @param string|null $competitionType
     *
     * @return Circuit
     */
    public function setCompetitionType(?string $competitionType): Circuit
    {
        $this->competitionType = $competitionType;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGroupSize(): ?int
    {
        return $this->groupSize;
    }

    /**
     * @param int|null $groupSize
     *
     * @return Circuit
     */
    public function setGroupSize(?int $groupSize): Circuit
    {
        $this->groupSize = $groupSize;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    /**
     * @param int|null $minAge
     *
     * @return Circuit
     */
    public function setMinAge(?int $minAge): Circuit
    {
        $this->minAge = $minAge;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    /**
     * @param int|null $maxAge
     *
     * @return Circuit
     */
    public function setMaxAge(?int $maxAge): Circuit
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Circuit
     */
    public function setDescription(?string $description): Circuit
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price / 100.0; // we store in cents
    }

    /**
     * @param float|null $price
     *
     * @return Circuit
     */
    public function setPrice(?float $price): Circuit
    {
        $this->price = $price * 100;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPoints(): ?int
    {
        return $this->points;
    }

    /**
     * @param int|null $points
     *
     * @return Circuit
     */
    public function setPoints(?int $points): Circuit
    {
        $this->points = $points;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDummy(): bool
    {
        return $this->dummy;
    }

    /**
     * @param bool $dummy
     *
     * @return Circuit
     */
    public function setDummy(bool $dummy): Circuit
    {
        $this->dummy = $dummy;
        return $this;
    }
}
