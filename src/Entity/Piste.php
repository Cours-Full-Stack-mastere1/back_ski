<?php

namespace App\Entity;

use App\Repository\PisteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PisteRepository::class)]
class Piste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllPiste"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllPiste"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllPiste"])]
    private ?string $couleur = null;

    #[ORM\Column]
    #[Groups(["getAllPiste"])]
    private ?bool $ouvert = null;

    #[ORM\Column]
    #[Groups(["getAllPiste"])]
    private ?int $longeur = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(["getAllPiste"])]
    private array $temps = [];

    #[ORM\ManyToOne(inversedBy: 'piste')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $station = null;

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

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function isOuvert(): ?bool
    {
        return $this->ouvert;
    }

    public function setOuvert(bool $ouvert): static
    {
        $this->ouvert = $ouvert;

        return $this;
    }

    public function getLongeur(): ?int
    {
        return $this->longeur;
    }

    public function setLongeur(int $longeur): static
    {
        $this->longeur = $longeur;

        return $this;
    }

    public function getTemps(): array
    {
        return $this->temps;
    }

    public function setTemps(array $temps): static
    {
        $this->temps = $temps;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): static
    {
        $this->station = $station;

        return $this;
    }
}