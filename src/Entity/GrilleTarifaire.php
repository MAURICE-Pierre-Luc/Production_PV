<?php

namespace App\Entity;

use App\Enum\CouleurJour;
use App\Repository\GrilleTarifaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrilleTarifaireRepository::class)]
class GrilleTarifaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column (name: 'id_tarif')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(length: 50, enumType: CouleurJour::class)]
    private ?CouleurJour $couleur = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $deb = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $tarif = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $fin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $puissance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getCouleur(): ?CouleurJour
    {
        return $this->couleur;
    }

    public function setCouleur(CouleurJour $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDeb(): ?\DateTime
    {
        return $this->deb;
    }

    public function setDeb(?\DateTime $deb): static
    {
        $this->deb = $deb;

        return $this;
    }

    public function getTarif(): ?string
    {
        return $this->tarif;
    }

    public function setTarif(?string $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setFin(?\DateTime $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->tarifHP;
    }

    public function setType(?string $tarifHP): static
    {
        $this->tarifHP = $tarifHP;

        return $this;
    }

    public function getPuissance(): ?int
    {
        return $this->puissance;
    }

    public function setPuissance(?int $puissance): static
    {
        $this->puissance = $puissance;

        return $this;
    }
}
