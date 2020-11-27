<?php


namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity
 * @ORM\Table(name="run")
 */
class Run
{
    public const CIRCUIT_LONG = 'long';
    public const CIRCUIT_MEDIUM = 'medium';
    public const CIRCUIT_SHORT = 'short';
    public const CIRCUIT_YOUTH = 'youth';

    /**
     * @var integer
     * @Groups({"from_run"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime
     * @Groups({"from_run"})
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var string
     * @Groups({"from_run"})
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @var integer
     * @Groups({"from_run"})
     * @ORM\Column(type="integer")
     */
    private $age = 0;

    /**
     * @var Organization
     * @Groups({"from_run"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization")
     */
    private $organization;

    /**
     * @var boolean
     * @Groups({"from_run"})
     * @ORM\Column(type="boolean")
     */
    private $cancelled = false;

    /**
     * subscribe url
     * @var string
     * @Groups({"from_run"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $subscribe;

    /**
     * results url
     * @var string
     * @Groups({"from_run"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $result;

    /**
     * @var ?Circuit[]
     * @Groups({"from_run"})
     * @ORM\OneToMany(targetEntity="App\Entity\Circuit", mappedBy="run")
     */
    private $circuits;

    public function __toString()
    {
        return sprintf('%s in %s', $this->date->format('Y-m-d'), $this->city);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @Groups({"from_run"})
     * @SerializedName("type")
     */
    public function getObjectType(): string
    {
        return 'run';
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return Run
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Run
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     * @return Run
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     * @return Run
     */
    public function setOrganization(Organization $organization)
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
     * @return Run
     */
    public function setCancelled(bool $cancelled): Run
    {
        $this->cancelled = $cancelled;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscribe()
    {
        return $this->subscribe;
    }

    /**
     * @param mixed $subscribe
     * @return Run
     */
    public function setSubscribe($subscribe)
    {
        $this->subscribe = $subscribe;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return Run
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return Circuit[]|null
     */
    public function getCircuits()
    {
        return $this->circuits;
    }
}
