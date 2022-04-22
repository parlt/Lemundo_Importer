<?php
declare(strict_types=1);

namespace Lemundo\Importer\Processor\Import;

use Lemundo\Importer\Api\ImportProcessorInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Psr\Log\LoggerInterface;

class CategoryProcessor implements ImportProcessorInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    private CategoryFactory $categoryFactory;

    private CategoryCollectionFactory $categoryCollectionFactory;

    private LoggerInterface $logger;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryFactory $categoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->logger = $logger;
    }

    public function process(array $data): void
    {
        $categories = $this->extractCategories($data);

        $existingCategories = $this->getExistingCategories($categories);
        foreach ($categories as $categoryItem) {

            $name = $categoryItem['name'];
            if (in_array($categoryItem['category_id'], $existingCategories)) {
                $this->logger->debug('category exists: ' . $name);
                continue;
            }

            $category = $this->categoryFactory->create();
            $category->setName($name);
            $category->setIsActive(true);
            $category->setData('lemundo_legacy_category_id', $categoryItem['category_id']);
            $category->setIncludeInMenu(true);

            try {
                $this->categoryRepository->save($category);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
    }

    private function getExistingCategories(array $categories): array
    {
        $ids = array_column($categories, 'category_id');

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToFilter('lemundo_legacy_category_id', ['in' => $ids]);

        $items = $collection->getItems();
        if (empty($items)) {
            return [];
        }

        $data = array_reduce($items, function ($accumulator, $item) {
            $id = $item->getData('lemundo_legacy_category_id');
            if (empty($accumulator[$id])) {
                $accumulator[$id] = $id;
            }
            return $accumulator;
        });
        return array_values($data);
    }

    private function extractCategories(array $data): array
    {
        $categories = [];
        foreach ($data as $item) {

            if (empty($item['_source']['category'])) {
                continue;
            }

            foreach ($item['_source']['category'] as $categoryItem) {
                $categories[$categoryItem['name']] = $categoryItem;

            }
        }
        return $categories;
    }
}
