<?php
declare(strict_types=1);

namespace Lemundo\Importer\Mapper\Product\PostSave;

use Lemundo\Importer\Api\ProductMapperInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Psr\Log\LoggerInterface;

class ProductCategoriesMapper implements ProductMapperInterface
{
    private CategoryCollectionFactory $categoryCollectionFactory;

    private CategoryLinkManagementInterface $categoryLinkManagement;

    private LoggerInterface $logger;

    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        LoggerInterface $logger
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->logger = $logger;
    }

    public function map(array $data, ProductInterface $product): ProductInterface
    {
        if (empty($data['category_ids'])) {
            $this->logger->debug('legacy product data has no category information');
            return $product;
        }

        $categoryIds = $this->getCategoriesByLegacyCategoryIds($data['category_ids']);
        if (empty($categoryIds)) {
            $this->logger->debug('categories missing');
            return $product;
        }

        $this->categoryLinkManagement->assignProductToCategories($product->getSku(), $categoryIds);

        return $product;
    }

    private function getCategoriesByLegacyCategoryIds(array $legacyCategoryIds): array
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('lemundo_legacy_category_id', ['in' => $legacyCategoryIds])
            ->getAllIds();
    }
}
