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
    #[Route('/api/grille-tarifaire/tarifs', name: 'api_grille_tarifaire_list', methods: ['GET'])]
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

    #[Route('/api/grille-tarifaire/period', name: 'api_grille_tarifaire_periode', methods: ['GET'])]
    public function parPeriode(Request $request, GrilleTarifaireRepository $repository): JsonResponse
    {
        $debut = \DateTime::createFromFormat('!Y-m-d', (string) $request->query->get('dateDebut'));
        $fin   = \DateTime::createFromFormat('!Y-m-d', (string) $request->query->get('dateFin'));

        if (!$debut || !$fin) {
            return $this->json(
                ['error' => 'Les paramètres dateDebut et dateFin sont obligatoires au format Y-m-d.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if ($debut > $fin) {
            return $this->json(
                ['error' => 'dateDebut doit être antérieure ou égale à dateFin.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $tarifs = $repository->findByPeriode($debut, $fin);

        $data = array_map(static fn (GrilleTarifaire $t) => [
            'id'         => $t->getId(),
            'dateDebut'  => $t->getDateDebut()?->format('Y-m-d'),
            'dateFin'    => $t->getDateFin()?->format('Y-m-d'),
            'couleur'    => $t->getCouleur()?->value,
            'deb'        => $t->getDeb()?->format('H:i:s'),
            'fin'        => $t->getFin()?->format('H:i:s'),
            'tarif'      => $t->getTarif(),
            'type'       => $t->getType()?->value,
            'puissance'  => $t->getPuissance(),
        ], $tarifs);

        return $this->json($data);
    }
}