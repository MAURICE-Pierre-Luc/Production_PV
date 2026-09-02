<?php

namespace App\Entity;

use App\Repository\ProductionJourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductionJourRepository::class)]
class ProductionJour
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'productionJour')]
    #[ORM\JoinColumn(name: 'date_jour', referencedColumnName: 'date_jour', nullable: false)]
    private ?Jour $jour = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieHP = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $puissanceMax = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $heurePuissanceMax = null;


    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getEnergieHC(): ?string
    {
        return $this->energieHC;
    }

    public function setEnergieHC(?string $energieHC): static
    {
        $this->energieHC = $energieHC;

        return $this;
    }

    public function getEnergieHP(): ?string
    {
        return $this->energieHP;
    }

    public function setEnergieHP(?string $energieHP): static
    {
        $this->energieHP = $energieHP;

        return $this;
    }

    public function getPuissanceMax(): ?string
    {
        return $this->puissanceMax;
    }

    public function setPuissanceMax(?string $puissanceMax): static
    {
        $this->puissanceMax = $puissanceMax;

        return $this;
    }

    public function getHeurePuissanceMax(): ?\DateTime
    {
        return $this->heurePuissanceMax;
    }

    public function setHeurePuissanceMax(?\DateTime $heurePuissanceMax): static
    {
        $this->heurePuissanceMax = $heurePuissanceMax;

        return $this;
    }
}
