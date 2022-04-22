<?php

namespace Lemundo\Importer\Mapper;

use Lemundo\Importer\Api\ProductMapperInterface;
use Lemundo\Importer\Api\ProductMapperPoolInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductMapperPool implements ProductMapperPoolInterface
{
    /** @var array ProductMapperInterface[] */
    private array $mappers;

    public function __construct(array $mappers = [])
    {
        $this->mappers = $mappers;
    }

    public function execute(array $data, ProductInterface $product): ProductInterface
    {
        foreach ($this->mappers as $mapper) {
            $mapper->map($data, $product);
        }

        return $product;
    }
}
