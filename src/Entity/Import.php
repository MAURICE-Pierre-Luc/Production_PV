<?php

namespace App\Entity;

use App\Enum\StatutImport;
use App\Repository\ImportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
class Import
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_import')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomFichier = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateImport = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateDebutDonnees = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateFinDonnees = null;

    #[ORM\Column(enumType: StatutImport::class)]
    private ?StatutImport $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(string $nomFichier): static
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getDateImport(): ?\DateTime
    {
        return $this->dateImport;
    }

    public function setDateImport(?\DateTime $dateImport): static
    {
        $this->dateImport = $dateImport;

        return $this;
    }

    public function getDateDebutDonnees(): ?\DateTime
    {
        return $this->dateDebutDonnees;
    }

    public function setDateDebutDonnees(?\DateTime $dateDebutDonnees): static
    {
        $this->dateDebutDonnees = $dateDebutDonnees;

        return $this;
    }

    public function getDateFinDonnees(): ?\DateTime
    {
        return $this->dateFinDonnees;
    }

    public function setDateFinDonnees(?\DateTime $dateFinDonnees): static
    {
        $this->dateFinDonnees = $dateFinDonnees;

        return $this;
    }

    public function getStatut(): ?StatutImport
    {
        return $this->statut;
    }

    public function setStatut(StatutImport $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
