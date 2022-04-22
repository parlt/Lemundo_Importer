<?php
declare(strict_types=1);

namespace Lemundo\Importer\Mapper\Product\PostSave;

use Lemundo\Importer\Api\ProductMapperInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;

class ProductStockMapper implements ProductMapperInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function map(array $data, ProductInterface $product): ProductInterface
    {
        if (empty($data['stock'])) {
            $this->logger->debug('stock data is empty');
            return $product;
        }
        $productStockItem = $product->getExtensionAttributes()->getStockItem();

        $productStockItem->setQty($data['stock']['qty']);
        $productStockItem->setIsInStock(true);

        // yes... I know its better to use the repro
        $productStockItem->save();
        return $product;
    }
}
