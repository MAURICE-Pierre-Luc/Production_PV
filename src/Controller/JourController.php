<?php

namespace App\Controller;

use App\Entity\Jour;
use App\Repository\JourRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class JourController extends AbstractController
{
    #[Route('/api/jour/period', name: 'api_jours_periode', methods: ['GET'])]
    public function parPeriode(Request $request, JourRepository $repository): JsonResponse
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

        $jours = $repository->findByPeriode($debut, $fin);

        $data = array_map(static fn (Jour $j) => [
            'id'       => $j->getId(),
            'dateJour' => $j->getDateJour()?->format('Y-m-d'),
            'couleur'  => $j->getCouleur()?->value,

            'production' => [
                'energieHC'          => $j->getProductionEnergieHC(),
                'energieHP'          => $j->getProductionEnergieHP(),
                'puissanceMax'       => $j->getProductionPuissanceMax(),
                'heurePuissanceMax'  => $j->getProductionHeurePuissanceMax()?->format('H:i:s'),
            ],

            'reseau' => [
                'energieImporteeHC' => $j->getEnergieImporteeHC(),
                'energieImporteeHP' => $j->getEnergieImporteeHP(),
            ],
            
            'batterie' => [
                'socMin'         => $j->getSocMin(),
                'socMax'         => $j->getSocMax(),
                'soh'            => $j->getSoh(),
                'temperatureMin' => $j->getTemperatureMin(),
                'temperatureMax' => $j->getTemperatureMax(),
                'nbAlarmes'      => $j->getNbAlarmes(),
            ],

            'vehiculeElectrique' => [
                'energieHC' => $j->getVeEnergieHC(),
                'energieHP' => $j->getVeEnergieHP(),
            ],
        ], $jours);

        return $this->json($data);
    }
}