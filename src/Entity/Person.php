<?php

namespace App\Entity;

use App\Entity\Person;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class Person implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(min: 2, max: 60, minMessage:"Le nom ne doit pas faire moins de 2 caractères", maxMessage: "Le nom ne doit pas faire plus de 60 caractères")]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 2, max: 50, minMessage:"Le prénom ne doit pas faire moins de 2 caractères", maxMessage: "Le prénom ne doit pas faire plus de 50 caractères")]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 2, max: 100, minMessage:"L'email ne doit pas faire moins de 2 caractères", maxMessage: "L'email ne doit pas faire plus de 50 caractères")]
    #[Assert\Email(message: "Veuillez renseigner une adresse e-mail valide")]
    private ?string $email = null;

    #[ORM\Column(length: 25, nullable: true)]
    #[Assert\Length(max: 5, maxMessage:"Le numero de telephone doit faire maximum 25 caractères")]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez renseigner votre addresse")]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    #[Assert\Length(max: 10, maxMessage:"Le numero doit faire maximum 10 caractères")]
    #[Assert\NotBlank(message: "Veuillez renseigner votre addresse")]
    private ?string $number = null;

    #[ORM\Column(length: 20)]
    #[Assert\Length(max: 10, maxMessage:"Le zip doit faire maximum 20 caractères")]
    #[Assert\NotBlank(message: "Veuillez renseigner votre zipcode")]
    private ?string $zip = null;

    #[ORM\Column(length: 70)]
    #[Assert\Length(max: 70, maxMessage:"La ville doit faire maximum 70 caractères")]
    #[Assert\NotBlank(message: "Veuillez renseigner votre ville")]
    private ?string $locality = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(max: 70, maxMessage:"Le pays doit faire maximum 50 caractères")]
    #[Assert\NotBlank(message: "Veuillez renseigner votre pays")]
    private ?string $country = null;

    #[ORM\Column]
    private ?bool $optin = null;

    #[ORM\ManyToOne(inversedBy: 'people')]
    private ?Building $building = null;


    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $owners;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $votes;

    /**
     * @var Collection<int, Issue>
     */
    #[ORM\OneToMany(targetEntity: Issue::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $issues;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->issues = new ArrayCollection();
    }

    /**
     * Permet de creer un slug automatiquement avec le nom et prenom de la personne
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name.' '.$this->firstname.' '.uniqid());
        }
    }

    /**
    * Permet de créer automatiquement un username unique
    *
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateUniqueUsername(): void
    {
        if (empty($this->username)) {
            // Génération du username basé sur un identifiant unique
            $username = 'user_' . uniqid();

            // Affectation du username généré
            $this->username = $username;
        }
    }

    /**
     * @return string Le nom complet de la personne (nom + prénom)
     */
    public function getFullName(): string
    {
        return $this->name . ' ' . $this->firstname;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): static
    {
        $this->locality = $locality;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function isOptin(): ?bool
    {
        return $this->optin;
    }

    public function setOptin(bool $optin): static
    {
        $this->optin = $optin;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Owner>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(Owner $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
            $owner->setPerson($this);
        }

        return $this;
    }

    public function removeOwner(Owner $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getPerson() === $this) {
                $owner->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setPerson($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getPerson() === $this) {
                $vote->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setPerson($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getPerson() === $this) {
                $issue->setPerson(null);
            }
        }

        return $this;
    }
}
