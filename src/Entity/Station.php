<?php

namespace App\Entity;

use Hateoas\Configuration\Annotation as Hateoas;
use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: StationRepository::class)]
/**
     * @Hateoas\Relation(
     *     "up",
     *    href = @Hateoas\Route(
     *        "station.getAll"
     * ),
     *  exclusion = @Hateoas\Exclusion(groups = {"getAllStation"})
     * )
     * * @Hateoas\Relation(
     *     "self",
     *   href = @Hateoas\Route(
     *       "station.get",
     *      parameters = {"idStation" = "expr(object.getId())"}
     * ),
     * exclusion = @Hateoas\Exclusion(groups = {"getAllStation"})
     * )
     * 
     * 
     * 
     * @OA\Schema(
    *     description="Station entity",
    *     title="Station",
    *     required={"nom", "gps"},
    *     @OA\Property(
    *         property="id",
    *         type="integer",
    *         description="id",
    *         example=1
    *     ),
    *     @OA\Property(
    *         property="nom",
    *         type="string",
    *         description="nom",
    *         example="Les Arcs"
    *     ),
    *     @OA\Property(
    *         property="gps",
    *         type="string",
    *         description="gps",
    *         example="45.5725, 6.7933"
    *     )
    * )
     **/
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllStation"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllStation"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllStation"])]
    private ?string $gps = null;

    #[ORM\OneToMany(mappedBy: 'station', targetEntity: Piste::class, orphanRemoval: true, /* cascade: ['persist'] */)]
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
