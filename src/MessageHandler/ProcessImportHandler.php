<?php

namespace App\MessageHandler;

use App\Message\ProcessImport;
use App\Service\VictronCsvProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessImportHandler
{
    public function __construct(
        private VictronCsvProcessor $processor
    ) {
    }

    public function __invoke(ProcessImport $message): void
    {
        $this->processor->traiter($message->getImportId());
    }
}
