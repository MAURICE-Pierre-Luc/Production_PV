<?php

namespace App\MessageHandler;

use App\Message\ProcessImport;
use App\Repository\ImportRepository;
use App\Service\VictronCsvProcessor;
use App\Enum\StatutImport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[AsMessageHandler]
class ProcessImportHandler
{
    public function __construct(
        private ImportRepository $importRepository,
        private VictronCsvProcessor $victronCsvProcessor,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ProcessImport $message): void
    {
        $import = $this->importRepository->find($message->getImportId());

        if ($import === null) {
            throw new \RuntimeException(
                'Import introuvable : ' . $message->getImportId()
            );
        }

        try {
            // L'import commence
            $import->setStatut(StatutImport::EN_COURS);

            $this->entityManager->flush();

            // Traitement du CSV
            try {
                $resultat = $this->victronCsvProcessor->traiterCSV( $message->getImportId());
            } catch (UniqueConstraintViolationException $e) {
                throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
            }

            $import->setDateDebutDonnees($resultat['dateDebut']);
            $import->setDateFinDonnees($resultat['dateFin']);

            // Traitement terminé
            $import->setStatut(StatutImport::TERMINE);

            $this->entityManager->flush();

        } catch (\Throwable $e) {

            $import->setStatut(StatutImport::ERREUR);

            $this->entityManager->flush();
            $this->entityManager->clear();

            throw $e;
        }
    }
}