<?php
declare(strict_types=1);

namespace Lemundo\Importer\Mapper\Product\PreSave;

use Lemundo\Importer\Api\ProductMapperInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductTaxMapper implements ProductMapperInterface
{
    public function map(array $data, ProductInterface $product): ProductInterface
    {
        $product->setData('tax_class_id', $data['tax_class_id']);
        return $product;
    }
}
