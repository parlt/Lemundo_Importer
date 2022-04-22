<?php
declare(strict_types=1);

namespace Lemundo\Importer\Api;

use Lemundo\Importer\Exception\ImportException;

interface ImportProcessorPoolInterface
{
    /**
     * @throws ImportException
     */
    public function execute(array $data): void;
}
