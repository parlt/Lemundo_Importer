<?php
declare(strict_types=1);

namespace Lemundo\Importer\Plugin\Magento\Catalog\Model\Category;

use Magento\Catalog\Model\Category\DataProvider;
use Magento\Eav\Model\Config;

class DataProviderPlugin
{
    private Config $eavConfig;

    public function __construct(Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    public function afterPrepareMeta(DataProvider $subject, $result): array
    {
        $data = $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'));

        $result['content']['children']['lemundo_legacy_category_id']['arguments']['data']['config'] = (
        $data['lemundo_legacy_category_id']
        );

        $result['content']['children'] = $this->sortData($result['content']['children']);

        return $result;
    }

    private function sortData(array $data): array
    {
        asort($data);
        return $data;
    }
}
