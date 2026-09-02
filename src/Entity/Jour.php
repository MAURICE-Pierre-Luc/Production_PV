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
    private ?CouleurJour $couleur = null;

    #[ORM\OneToOne(mappedBy: 'jour')]
    private ?ProductionJour $productionJour = null;

    #[ORM\OneToOne(mappedBy: 'jour')]
    private ?ConsommationJour $consommationJour = null;

    #[ORM\OneToOne(mappedBy: 'jour')]
    private ?BatterieJour $batterieJour = null;

    #[ORM\OneToOne(mappedBy: 'jour')]
    private ?VeJour $veJour = null;

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

    public function getProductionJour(): ?ProductionJour
    {
        return $this->productionJour;
    }

    public function setProductionJour(?ProductionJour $productionJour): static
    {
        $this->productionJour = $productionJour;

        return $this;
    }

    public function getConsommationJour(): ?ConsommationJour
    {
        return $this->consommationJour;
    }

    public function setConsommationJour(?ConsommationJour $consommationJour): static
    {
        $this->consommationJour = $consommationJour;

        return $this;
    }

    public function getBatterieJour(): ?BatterieJour
    {
        return $this->batterieJour;
    }

    public function setBatterieJour(?BatterieJour $batterieJour): static
    {
        $this->batterieJour = $batterieJour;

        return $this;
    }

    public function getVeJour(): ?VeJour
    {
        return $this->veJour;
    }

    public function setVeJour(?VeJour $veJour): static
    {
        $this->veJour = $veJour;

        return $this;
    }








}
