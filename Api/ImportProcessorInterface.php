<?php
declare(strict_types=1);

namespace Lemundo\Importer\Api;

use Lemundo\Importer\Exception\ImportException;

interface ImportProcessorInterface
{
    /**
     * @throws ImportException
     */
    public function process(array $data): void;
}
