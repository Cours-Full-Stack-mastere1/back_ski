<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $gps = null;

    #[ORM\OneToMany(mappedBy: 'station', targetEntity: Piste::class, orphanRemoval: true)]
    private Collection $piste;

    public function __construct()
    {
        $this->piste = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getGps(): ?string
    {
        return $this->gps;
    }

    public function setGps(string $gps): static
    {
        $this->gps = $gps;

        return $this;
    }

    /**
     * @return Collection<int, Piste>
     */
    public function getPiste(): Collection
    {
        return $this->piste;
    }

    public function addPiste(Piste $piste): static
    {
        if (!$this->piste->contains($piste)) {
            $this->piste->add($piste);
            $piste->setStation($this);
        }

        return $this;
    }

    public function removePiste(Piste $piste): static
    {
        if ($this->piste->removeElement($piste)) {
            // set the owning side to null (unless already changed)
            if ($piste->getStation() === $this) {
                $piste->setStation(null);
            }
        }

        return $this;
    }
}
