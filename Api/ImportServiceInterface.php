<?php
declare(strict_types=1);

namespace Lemundo\Importer\Api;

use Lemundo\Importer\Exception\ImportException;

interface ImportServiceInterface
{
    /**
     * @throws ImportException
     */
    public function execute(string $data): void;
}
