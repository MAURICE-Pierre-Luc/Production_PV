<?php

namespace App\Entity;

use App\Repository\BatterieJourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BatterieJourRepository::class)]
class BatterieJour
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'batterieJour')]
    #[ORM\JoinColumn(name: 'date_jour', referencedColumnName: 'date_jour', nullable: false)]
    private ?Jour $jour = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $socMoyen = null;

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

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getSocMoyen(): ?string
    {
        return $this->socMoyen;
    }

    public function setSocMoyen(?string $socMoyen): static
    {
        $this->socMoyen = $socMoyen;

        return $this;
    }

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
}
