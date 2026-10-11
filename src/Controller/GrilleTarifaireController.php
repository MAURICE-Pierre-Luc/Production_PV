<?php

namespace App\Controller;

use App\Entity\GrilleTarifaire;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GrilleTarifaireController extends AbstractController
{
    #[Route('/api/grille-tarifaire', name: 'api_grille_tarifaire_list', methods: ['GET'])]
    public function list(GrilleTarifaireRepository $grilleTarifaireRepository): JsonResponse
    {
        $grilles = $grilleTarifaireRepository->findAll();

        $data = array_map(static fn ($grille) => [
            'id'         => $grille->getId(),
            'dateDebut'  => $grille->getDateDebut()?->format('Y-m-d'),
            'dateFin'    => $grille->getDateFin()?->format('Y-m-d'),
            'couleur'    => $grille->getCouleur()?->value,
            'tarif'      => $grille->getTarif(),
            'type'       => $grille->getType()?->value,
            'puissance'  => $grille->getPuissance(),
        ], $grilles);

        return $this->json($data);
    }

    #[Route('/api/grille-tarifaire/puissances', name: 'api_grille_tarifaire_puissances', methods: ['GET'])]
    public function puissances(GrilleTarifaireRepository $grilleTarifaireRepository): JsonResponse
    {
        return $this->json($grilleTarifaireRepository->findDistinctPuissances());
    }

    #[Route('/api/grille-tarifaire/bornes-dates', name: 'api_grille_tarifaire_bornes_dates', methods: ['GET'])]
    public function bornesDates(GrilleTarifaireRepository $grilleTarifaireRepository): JsonResponse
    {
        return $this->json($grilleTarifaireRepository->findBornesDates());
    }
    
}