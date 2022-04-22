<?php
declare(strict_types=1);

namespace Lemundo\Importer\Processor;

use Lemundo\Importer\Api\ImportProcessorInterface;
use Lemundo\Importer\Api\ImportProcessorPoolInterface;
use Lemundo\Importer\Exception\ImportException;

class ImportProcessorPool implements ImportProcessorPoolInterface
{
    /** @var ImportProcessorInterface[] */
    private array $importProcessors;

    public function __construct(array $importProcessors = [])
    {
        $this->importProcessors = $importProcessors;
    }

    /**
     * @throws ImportException
     */
    public function execute(array $data): void
    {
        foreach ($this->importProcessors as $importProcessor) {
            $importProcessor->process($data);
        }
    }
}
