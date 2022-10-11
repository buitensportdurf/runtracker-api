<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    /**
     * @var integer
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $username;

    /**
     * @var ?string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $firstName;

    /**
     * @var ?string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $middleName;

    /**
     * @var ?string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $lastName;

    /**
     * @var ?string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $gender;

    /**
     * @var ?string
     */
    #[Groups(['from_circuit', 'from_run'])]
    #[ORM\Column(type: 'string', nullable: true)]
    private $city;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $profilePicture = null;

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['from_run'])]
    #[SerializedName('type')]
    public function getObjectType(): string
    {
        return 'user';
    }

    #[Groups(['from_run'])]
    public function getProfilePictureUrl(): string
    {
        if ($this->profilePicture) {
            return sprintf('https://api.survivalruns.nl/image/%d', $this->profilePicture->getId());
        }

        return '';
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return User
     */
    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param string|null $middleName
     *
     * @return User
     */
    public function setMiddleName(?string $middleName): User
    {
        $this->middleName = $middleName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     *
     * @return User
     */
    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     *
     * @return User
     */
    public function setGender(?string $gender): User
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return User
     */
    public function setCity(?string $city): User
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getProfilePicture(): ?Image
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?Image $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }
}
