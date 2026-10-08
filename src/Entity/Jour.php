<?php

namespace App\Entity;

use App\Enum\CouleurJour;
use App\Repository\JourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JourRepository::class)]
class Jour
{
    #[ORM\Id]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateJour = null;

    #[ORM\Column(length: 50, enumType: CouleurJour::class)]
    private CouleurJour $couleur = CouleurJour::INCONNU;

    // Production solaire

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $productionEnergieHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $productionEnergieHP = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $productionPuissanceMax = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $productionHeurePuissanceMax = null;

    // Consommation du réseau électrique

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieImporteeHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieImporteeHP = null;

    // Batterie

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $socMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $socMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $soh = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $temperatureMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $temperatureMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbAlarmes = null;

    // Recharge du véhicule électrique

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $veEnergieHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $veEnergieHP = null;

    public function getDateJour(): ?\DateTime
    {
        return $this->dateJour;
    }

    public function setDateJour(\DateTime $dateJour): static
    {
        $this->dateJour = $dateJour;

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

    // Production solaire

    public function getProductionEnergieHC(): ?string
    {
        return $this->productionEnergieHC;
    }

    public function setProductionEnergieHC(?string $energie): static
    {
        $this->productionEnergieHC = $energie;

        return $this;
    }

    public function getProductionEnergieHP(): ?string
    {
        return $this->productionEnergieHP;
    }

    public function setProductionEnergieHP(?string $energie): static
    {
        $this->productionEnergieHP = $energie;

        return $this;
    }

    public function getProductionPuissanceMax(): ?string
    {
        return $this->productionPuissanceMax;
    }

    public function setProductionPuissanceMax(?string $puissance): static
    {
        $this->productionPuissanceMax = $puissance;

        return $this;
    }

    public function getProductionHeurePuissanceMax(): ?\DateTime
    {
        return $this->productionHeurePuissanceMax;
    }

    public function setProductionHeurePuissanceMax(?\DateTime $heure): static
    {
        $this->productionHeurePuissanceMax = $heure;

        return $this;
    }

    // Consommation du réseau électrique

    public function getEnergieImporteeHC(): ?string
    {
        return $this->energieImporteeHC;
    }

    public function setEnergieImporteeHC(?string $energie): static
    {
        $this->energieImporteeHC = $energie;

        return $this;
    }

    public function getEnergieImporteeHP(): ?string
    {
        return $this->energieImporteeHP;
    }

    public function setEnergieImporteeHP(?string $energie): static
    {
        $this->energieImporteeHP = $energie;

        return $this;
    }

    // Batterie

    public function getSocMin(): ?string
    {
        return $this->socMin;
    }

    public function setSocMin(?string $socMin): static
    {
        $this->socMin = $socMin;

        return $this;
    }

    public function getSocMax(): ?string
    {
        return $this->socMax;
    }

    public function setSocMax(?string $socMax): static
    {
        $this->socMax = $socMax;

        return $this;
    }

    public function getSoh(): ?string
    {
        return $this->soh;
    }

    public function setSoh(?string $soh): static
    {
        $this->soh = $soh;

        return $this;
    }

    public function getTemperatureMin(): ?string
    {
        return $this->temperatureMin;
    }

    public function setTemperatureMin(?string $temperatureMin): static
    {
        $this->temperatureMin = $temperatureMin;

        return $this;
    }

    public function getTemperatureMax(): ?string
    {
        return $this->temperatureMax;
    }

    public function setTemperatureMax(?string $temperatureMax): static
    {
        $this->temperatureMax = $temperatureMax;

        return $this;
    }

    public function getNbAlarmes(): ?int
    {
        return $this->nbAlarmes;
    }

    public function setNbAlarmes(?int $nbAlarmes): static
    {
        $this->nbAlarmes = $nbAlarmes;

        return $this;
    }

    // Recharge du véhicule électrique

    public function getVeEnergieHC(): ?string
    {
        return $this->veEnergieHC;
    }

    public function setVeEnergieHC(?string $energie): static
    {
        $this->veEnergieHC = $energie;

        return $this;
    }

    public function getVeEnergieHP(): ?string
    {
        return $this->veEnergieHP;
    }

    public function setVeEnergieHP(?string $energie): static
    {
        $this->veEnergieHP = $energie;

        return $this;
    }
}