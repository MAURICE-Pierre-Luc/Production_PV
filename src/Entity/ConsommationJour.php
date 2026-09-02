<?php

namespace App\Entity;

use App\Repository\ConsommationJourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsommationJourRepository::class)]
class ConsommationJour
{
   #[ORM\Id]
   #[ORM\OneToOne(inversedBy: 'consommationJour')]
   #[ORM\JoinColumn(name: 'date_jour', referencedColumnName: 'date_jour', nullable: false)]
   private ?Jour $jour = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieImporteeHC = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 3, nullable: true)]
    private ?string $energieImporteeHP = null;

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getEnergieImporteeHC(): ?string
    {
        return $this->energieImporteeHC;
    }

    public function setEnergieImporteeHC(?string $energieImporteeHC): static
    {
        $this->energieImporteeHC = $energieImporteeHC;

        return $this;
    }

    public function getEnergieImporteeHP(): ?string
    {
        return $this->energieImporteeHP;
    }

    public function setEnergieImporteeHP(?string $energieImporteeHP): static
    {
        $this->energieImporteeHP = $energieImporteeHP;

        return $this;
    }
}
