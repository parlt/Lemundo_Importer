<?php
declare(strict_types=1);

namespace Lemundo\Importer\Processor\Import;

use Lemundo\Importer\Api\ImportProcessorInterface;

use Lemundo\Importer\Api\ProductMapperPoolInterface;
use Lemundo\Importer\Config\DefaultConfig;
use Lemundo\Importer\Exception\ImportException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Psr\Log\LoggerInterface;

class ProductProcessor implements ImportProcessorInterface
{
    private ProductRepositoryInterface $productRepository;

    private ProductFactory $productFactory;

    private ProductMapperPoolInterface $productPreSaveMapperPool;

    private ProductMapperPoolInterface $productPostSaveMapperPool;

    private AttributeSetCollectionFactory $attributeSetCollectionFactory;

    private DefaultConfig $config;

    private LoggerInterface $logger;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        ProductMapperPoolInterface $productPreSaveMapperPool,
        ProductMapperPoolInterface $productPostSaveMapperPool,
        AttributeSetCollectionFactory $attributeSetCollectionFactory,
        DefaultConfig $config,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productPreSaveMapperPool = $productPreSaveMapperPool;
        $this->productPostSaveMapperPool = $productPostSaveMapperPool;
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @throws ImportException
     */
    public function process(array $data): void
    {
        $attributeSetId = $this->getAttributeSetId();
        foreach ($data as $item) {
            $source = $item['_source'];
            $product = $this->getOrCreateProduct($source);
            $product->setAttributeSetId($attributeSetId);

            $this->productPreSaveMapperPool->execute($source, $product);

            $product = $this->persistProduct($product);

            $this->productPostSaveMapperPool->execute($source, $product);
        }
    }

    private function getOrCreateProduct(array $data): ProductInterface
    {
        try {
            return $this->productRepository->get($data['sku']);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return $this->productFactory->create();
        }
    }

    /**
     * @throws ImportException
     */
    private function persistProduct(ProductInterface $product): ProductInterface
    {
        try {
            return $this->productRepository->save($product);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            throw new ImportException($e->getMessage());
        }
    }

    public function getAttributeSetId(): string
    {
        return $this->attributeSetCollectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter(
                'attribute_set_name',
                $this->config->getLandingPageAttributeSetName()
            )
            ->getFirstItem()
            ->getData('attribute_set_id');
    }
}
