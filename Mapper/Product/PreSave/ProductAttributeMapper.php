<?php
declare(strict_types=1);

namespace Lemundo\Importer\Mapper\Product\PreSave;

use Lemundo\Importer\Api\ProductMapperInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;

class ProductAttributeMapper implements ProductMapperInterface
{
    public function map(array $data, ProductInterface $product): ProductInterface
    {
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setPrice((float)$data['price']);
        $product->setData('lemundo_legacy_product_id', $data['id']);
        $product->setSku($data['sku']);
        $product->setWeight((float)$data['weight']);
        $product->setName($data['name']);

        if (!empty($data['description'])) {
            $product->setData('description', $data['description']);
        }

        if (!empty($data['lemundo_product_features'])) {
            $product->setData('lemundo_product_features', $data['product_features']);
        }

        if (!empty($data['lemundo_product_application'])) {
            $product->setData('lemundo_product_application', $data['document_applications']);
        }

        $product->setData('lemundo_landingpage_relevant', true);

        return $product;
    }
}
