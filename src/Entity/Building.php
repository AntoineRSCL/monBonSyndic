<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Building
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 10, max: 100, minMessage:"Le nom doit faire plus de 10 caractères", maxMessage: "Le nom ne doit pas faire plus de 100 caractères")]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 10, max: 255, minMessage:"L'adresse doit faire plus de 10 caractères", maxMessage: "L'adresse ne doit pas faire plus de 255 caractères")]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    #[Assert\Length(min: 1, max: 10, minMessage:"Le numéro doit faire plus de 1 caractère", maxMessage: "Le numéro ne doit pas faire plus de 10 caractères")]
    private ?string $number = null;

    #[ORM\Column(length: 4)]
    #[Assert\Length(min: 1, max: 4, minMessage:"Le zip doit faire plus de 1 caractère", maxMessage: "Le zip ne doit pas faire plus de 4 caractères")]
    private ?string $zip = null;

    #[ORM\Column(length: 70)]
    #[Assert\Length(min: 1, max: 70, minMessage:"La commune doit faire plus de 1 caractère", maxMessage: "La commune ne doit pas faire plus de 70 caractères")]
    private ?string $locality = null;

    #[ORM\Column]
    private ?int $quota = null;

    /**
     * @var Collection<int, Apartment>
     */
    #[ORM\OneToMany(targetEntity: Apartment::class, mappedBy: 'building', orphanRemoval: true)]
    private Collection $apartments;

    /**
     * @var Collection<int, Person>
     */
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'building')]
    private Collection $people;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    /**
     * @var Collection<int, Survey>
     */
    #[ORM\OneToMany(targetEntity: Survey::class, mappedBy: 'building')]
    private Collection $surveys;

    public function __construct()
    {
        $this->apartments = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->surveys = new ArrayCollection();
    }

    /**
     * Permet de creer un slug automatiquement avec le nom de l'immeuble
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug()
    {
        if(empty($this->slug))
        {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getQuota(): ?int
    {
        return $this->quota;
    }

    public function setQuota(int $quota): static
    {
        $this->quota = $quota;

        return $this;
    }

    /**
     * @return Collection<int, Apartment>
     */
    public function getApartments(): Collection
    {
        return $this->apartments;
    }

    public function addApartment(Apartment $apartment): static
    {
        if (!$this->apartments->contains($apartment)) {
            $this->apartments->add($apartment);
            $apartment->setBuilding($this);
        }

        return $this;
    }

    public function removeApartment(Apartment $apartment): static
    {
        if ($this->apartments->removeElement($apartment)) {
            // set the owning side to null (unless already changed)
            if ($apartment->getBuilding() === $this) {
                $apartment->setBuilding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setBuilding($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getBuilding() === $this) {
                $person->setBuilding(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Survey>
     */
    public function getSurveys(): Collection
    {
        return $this->surveys;
    }

    public function addSurvey(Survey $survey): static
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys->add($survey);
            $survey->setBuilding($this);
        }

        return $this;
    }

    public function removeSurvey(Survey $survey): static
    {
        if ($this->surveys->removeElement($survey)) {
            // set the owning side to null (unless already changed)
            if ($survey->getBuilding() === $this) {
                $survey->setBuilding(null);
            }
        }

        return $this;
    }    
}
