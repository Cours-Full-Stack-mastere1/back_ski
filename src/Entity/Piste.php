<?php

namespace App\Entity;

use App\Repository\PisteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: PisteRepository::class)]
/** 
 * @Hateoas\Relation(
 *    "up",
 *  href = @Hateoas\Route(
 * "station.piste",
 * parameters = {"idStation" = "expr(object.getStation().getId())"}
 * ),
 * exclusion = @Hateoas\Exclusion(groups = {"getAllPiste"})
 * )
 * @Hateoas\Relation(
 *   "self",
 * href = @Hateoas\Route(
 * "station.piste.get",
 * parameters = {"idStation" = "expr(object.getStation().getId())", "idPiste" = "expr(object.getId())"}
 * ),
 * exclusion = @Hateoas\Exclusion(groups = {"getAllPiste"})
 * )
 * 
 * @OA\Schema(
 *    description="Piste entity",
 *   title="Piste",
 *  required={"nom", "couleur", "ouvert", "longeur", "temps"},
 * @OA\Property(
 *    property="id",
 *  type="integer", *  
 * description="id",
 * example=1
 * ),
 * @OA\Property(
 *   property="nom",
 * type="string",
 * description="nom",
 * example="Les Chamois"
 * ),
 * @OA\Property(
 *  property="couleur",
 * type="string",
 * description="couleur",
 * example="bleu"
 * ),
 * @OA\Property(
 *  property="ouvert",
 * type="boolean",
 * description="ouvert",
 * example=true
 * ),
 * @OA\Property(
 * property="longeur",
 * type="integer",
 * description="longeur",
 * example=1000
 * ),
 * @OA\Property(
 * property="temps",
 * type="integer",
 * description="temps",
 * example=50
 * )
 * )
 * )
 * 
 */
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

    #[ORM\Column]
    #[Groups(["getAllPiste"])]
    private ?int $temps = null;

    #[ORM\ManyToOne(inversedBy: 'piste'/* , cascade: ['persist'] */)]
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

    public function getTemps(): ?int
    {
        return $this->temps;
    }

    public function setTemps(?int $temps): static
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
