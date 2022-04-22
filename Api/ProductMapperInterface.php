<?php
declare(strict_types=1);

namespace Lemundo\Importer\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductMapperInterface
{
    public function map(array $data, ProductInterface $product): ProductInterface;

}
