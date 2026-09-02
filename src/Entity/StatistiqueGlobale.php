<?php

namespace App\Entity;

use App\Repository\StatistiqueGlobaleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatistiqueGlobaleRepository::class)]
class StatistiqueGlobale
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieTotaleProduite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieTotaleImportee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieTotaleVE = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $socMaxObserve = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $sohMinObserve = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $puissanceMaxProduction = null;

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getEnergieTotaleProduite(): ?string
    {
        return $this->energieTotaleProduite;
    }

    public function setEnergieTotaleProduite(?string $energieTotaleProduite): static
    {
        $this->energieTotaleProduite = $energieTotaleProduite;

        return $this;
    }

    public function getEnergieTotaleImportee(): ?string
    {
        return $this->energieTotaleImportee;
    }

    public function setEnergieTotaleImportee(?string $energieTotaleImportee): static
    {
        $this->energieTotaleImportee = $energieTotaleImportee;

        return $this;
    }

    public function getEnergieTotaleVE(): ?string
    {
        return $this->energieTotaleVE;
    }

    public function setEnergieTotaleVE(?string $energieTotaleVE): static
    {
        $this->energieTotaleVE = $energieTotaleVE;

        return $this;
    }

    public function getSocMaxObserve(): ?string
    {
        return $this->socMaxObserve;
    }

    public function setSocMaxObserve(?string $socMaxObserve): static
    {
        $this->socMaxObserve = $socMaxObserve;

        return $this;
    }

    public function getSohMinObserve(): ?string
    {
        return $this->sohMinObserve;
    }

    public function setSohMinObserve(?string $sohMinObserve): static
    {
        $this->sohMinObserve = $sohMinObserve;

        return $this;
    }

    public function getPuissanceMaxProduction(): ?string
    {
        return $this->puissanceMaxProduction;
    }

    public function setPuissanceMaxProduction(?string $puissanceMaxProduction): static
    {
        $this->puissanceMaxProduction = $puissanceMaxProduction;

        return $this;
    }
}
