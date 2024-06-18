<?php

namespace App\Entity;

use App\Repository\ApartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApartmentRepository::class)]
class Apartment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'apartments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[ORM\Column(length: 5)]
    #[Assert\Length(max: 5, maxMessage:"La réference doit faire maximum 5 caractères")]
    private ?string $reference = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    private ?int $floor = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    private ?int $quota1 = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    private ?int $quota2 = null;

    /**
     * @var Collection<int, Owner>
     */
    #[ORM\OneToMany(targetEntity: Owner::class, mappedBy: 'apartment', orphanRemoval: true)]
    private Collection $owners;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function getQuota1(): ?int
    {
        return $this->quota1;
    }

    public function setQuota1(int $quota1): static
    {
        $this->quota1 = $quota1;

        return $this;
    }

    public function getQuota2(): ?int
    {
        return $this->quota2;
    }

    public function setQuota2(int $quota2): static
    {
        $this->quota2 = $quota2;

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
            $owner->setApartment($this);
        }

        return $this;
    }

    public function removeOwner(Owner $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getApartment() === $this) {
                $owner->setApartment(null);
            }
        }

        return $this;
    }

}
