<?php
declare(strict_types=1);

namespace Lemundo\Importer\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductMapperPoolInterface
{
    public function execute(array $data, ProductInterface $product): ProductInterface;
}
