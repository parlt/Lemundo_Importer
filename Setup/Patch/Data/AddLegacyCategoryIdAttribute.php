<?php
declare(strict_types=1);

namespace Lemundo\Importer\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddLegacyCategoryIdAttribute implements DataPatchInterface, PatchRevertableInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private CategorySetupFactory $categorySetupFactory;


    public function __construct(ModuleDataSetupInterface $moduleDataSetup, CategorySetupFactory $categorySetupFactory)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $categorySetup->addAttribute(
            Category::ENTITY,
            'lemundo_legacy_category_id',
            [
                'type' => 'varchar',
                'label' => 'Legacy category id',
                'input' => 'text',
                'frontend' => null,
                'required' => false,
                'sort_order' => 6,
                'visible' => false,
                'user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'content',
            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $categorySetup->removeAttribute(
            Category::ENTITY,
            'lemundo_legacy_category_id'
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }
}
