<?php
declare(strict_types=1);

namespace Lemundo\Importer\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddLandingPageProductAttributes implements DataPatchInterface, PatchRevertableInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup, EavSetupFactory $eavSetupFactory)
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public static function getDependencies()
    {
        return [
            AddLandingPageProductAttributeSet::class
        ];
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_product_features');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'lemundo_product_features',
            [
                'type' => 'varchar',
                'label' => 'Product Features',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
            ]
        );

        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_product_application');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'lemundo_product_application',
            [
                'type' => 'varchar',
                'label' => 'Product Application',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
            ]
        );

        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_landingpage_relevant');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'lemundo_landingpage_relevant',
            [
                'type' => 'int',
                'label' => 'Landingpage relevant',
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => true,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'used_in_product_listing' => false,
                'visible_on_front' => false,
                'default' => '0'
            ]
        );

        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_legacy_product_id');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'lemundo_legacy_product_id',
            [
                'type' => 'varchar',
                'label' => 'Legacy product id',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_product_features');
        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_product_application');
        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_landingpage_relevant');
        $eavSetup->removeAttribute(Product::ENTITY, 'lemundo_legacy_product_id');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }
}
