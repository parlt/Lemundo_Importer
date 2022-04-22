<?php
declare(strict_types=1);

namespace Lemundo\Importer\Service;

use Lemundo\Importer\Api\ImportProcessorPoolInterface;
use Lemundo\Importer\Api\ImportServiceInterface;
use Lemundo\Importer\Exception\ImportException;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Lemundo\Importer\Config\DefaultConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class ImportService implements ImportServiceInterface
{
    private ImportProcessorPoolInterface $importProcessorPoolInterface;

    private Json $json;

    private State $state;

    private DefaultConfig $config;

    private LoggerInterface $logger;

    public function __construct(
        ImportProcessorPoolInterface $importProcessorPoolInterface,
        Json $json,
        State $state,
        DefaultConfig $config,
        LoggerInterface $logger
    ) {
        $this->importProcessorPoolInterface = $importProcessorPoolInterface;
        $this->json = $json;
        $this->state = $state;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @throws ImportException
     */
    public function execute(string $data): void
    {
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (LocalizedException $e) {
            $this->logger->debug("Area Code already set.");
        }

        $items = $this->getArrayData($data);
        $this->importProcessorPoolInterface->execute($items);
    }

    private function getArrayData(string $data): array
    {
        $items = $this->json->unserialize($data);
        foreach ($this->getJsonStartIndex() as $index) {
            $items = $items[$index];

        }
        return $items;
    }

    private function getJsonStartIndex(): array
    {
        return explode('.', $this->config->getJsonStartIndex());
    }
}
