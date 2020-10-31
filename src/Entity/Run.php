<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $city;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $circuits = [];

    /**
     * @ORM\Column(type="simple_array")
     */
    private $distances = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $age = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization")
     */
    private $organization;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $subscribe;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $result;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getCircuits()
    {
        return $this->circuits;
    }

    /**
     * @param mixed $circuits
     * @return Run
     */
    public function setCircuits($circuits)
    {
        $this->circuits = $circuits;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistances()
    {
        return $this->distances;
    }

    /**
     * @param mixed $distances
     * @return Run
     */
    public function setDistances($distances)
    {
        $this->distances = $distances;
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
}