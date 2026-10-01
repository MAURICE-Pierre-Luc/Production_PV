<?php

namespace App\Controller;

use App\Entity\Import;
use App\Enum\StatutImport;
use App\Message\ProcessImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class ImportController extends AbstractController
{
    #[Route('/api/import', name: 'app_import', methods: ['POST'])]
    public function import(
        Request $request,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus
    ): JsonResponse {
        $fichier = $request->files->get('file');

        if (!$fichier) {
            return $this->json([
                'error' => 'Aucun fichier fourni.'
            ], 400);
        }

        $nomFichier = $fichier->getClientOriginalName();

        $import = new Import();
        $import->setNomFichier($nomFichier);
        $import->setDateImport(new \DateTime());
        $import->setStatut(StatutImport::EN_ATTENTE);

        $entityManager->persist($import);
        $entityManager->flush();

        try {
            $fichier->move(
                $this->getParameter('kernel.project_dir') . '/var/imports',
                $import->getId() . '_' . $nomFichier
            );
        } catch (FileException $e) {
            $import->setStatut(StatutImport::ERREUR);

            $entityManager->flush();

            return $this->json([
                'error' => 'Impossible de sauvegarder le fichier.'
            ], 500);
        }

        $bus->dispatch(new ProcessImport($import->getId()));

        return $this->json([
            'importId' => $import->getId(),
            'statut' => $import->getStatut()->value
        ]);
    }
}
