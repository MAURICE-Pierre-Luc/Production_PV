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
    private ?\DateTime $debHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $tarifHC = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $debHP = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $tarifHP = null;

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

    public function getDebHC(): ?\DateTime
    {
        return $this->debHC;
    }

    public function setDebHC(?\DateTime $debHC): static
    {
        $this->debHC = $debHC;

        return $this;
    }

    public function getTarifHC(): ?string
    {
        return $this->tarifHC;
    }

    public function setTarifHC(?string $tarifHC): static
    {
        $this->tarifHC = $tarifHC;

        return $this;
    }

    public function getDebHP(): ?\DateTime
    {
        return $this->debHP;
    }

    public function setDebHP(?\DateTime $debHP): static
    {
        $this->debHP = $debHP;

        return $this;
    }

    public function getTarifHP(): ?string
    {
        return $this->tarifHP;
    }

    public function setTarifHP(?string $tarifHP): static
    {
        $this->tarifHP = $tarifHP;

        return $this;
    }
}
