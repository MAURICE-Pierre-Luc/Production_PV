<?php

namespace App\Message;

final class ProcessImport
{
    public function __construct( private int $importId) {}

    public function getImportId(): int{
        return $this->importId;
    }
}
