<?php

namespace App\Controller;

use App\Entity\Import;
use App\Enum\StatutImport;
use App\Message\ProcessImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        // Vérification de la présence du fichier
        if (!$fichier instanceof UploadedFile || !$fichier->isValid()) {
            return $this->json([
                'error' => 'Aucun fichier valide fourni.'
            ], 400);
        }

        // Vérification de l'extension
        if (strtolower($fichier->getClientOriginalExtension()) !== 'csv') {
            return $this->json([
                'error' => 'Le fichier doit être au format CSV.'
            ], 400);
        }

        $nomFichier = $fichier->getClientOriginalName();

        // Création de l'import en base
        $import = new Import();
        $import->setNomFichier($nomFichier);
        $import->setDateImport(new \DateTime());
        $import->setStatut(StatutImport::EN_ATTENTE);

        $entityManager->persist($import);
        $entityManager->flush();

        // Enregistrement du fichier sur le serveur
        $dossier = $this->getParameter('kernel.project_dir') . '/var/imports';

        try {
            if (!is_dir($dossier)) {
                mkdir($dossier, 0775, true);
            }

            // Le nom de stockage ne dépend pas du nom fourni par l'utilisateur
            $nomStockage = $import->getId() . '.csv';

            $fichier->move($dossier, $nomStockage);

        } catch (FileException $e) {
            $import->setStatut(StatutImport::ERREUR);
            $entityManager->flush();

            return $this->json([
                'error' => 'Impossible de sauvegarder le fichier.'
            ], 500);
        }

        // Envoi du message à Messenger
        try {
            $bus->dispatch(new ProcessImport($import->getId()));

        } catch (\Throwable $e) {
            $import->setStatut(StatutImport::ERREUR);
            $entityManager->flush();

            return $this->json([
                'error' => 'Impossible de lancer le traitement du fichier.'
            ], 500);
        }

        // Confirmation de la réception
        return $this->json([
            'importId' => $import->getId(),
            'statut' => $import->getStatut()->value
        ], 202);
    }
}