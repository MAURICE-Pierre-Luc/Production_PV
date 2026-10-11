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
}