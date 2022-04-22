<?php
declare(strict_types=1);

namespace Lemundo\Importer\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AssignLandingPageProductAttributesToAttributeSet implements DataPatchInterface, PatchRevertableInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);

        $setId = $this->getAttributeSetId($eavSetup, $entityTypeId, 'Landingpage Products');
        $groupId = $this->getAttributeGroupId($eavSetup, $entityTypeId, $setId, 'product-details');

        $attributeId = $this->getAttributeId($eavSetup, $entityTypeId, 'lemundo_product_features');

        $eavSetup->addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId);

        $attributeId = $this->getAttributeId($eavSetup, $entityTypeId, 'lemundo_product_application');
        $eavSetup->addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId);

        $attributeId = $this->getAttributeId($eavSetup, $entityTypeId, 'lemundo_landingpage_relevant');
        $eavSetup->addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId);

        $attributeId = $this->getAttributeId($eavSetup, $entityTypeId, 'lemundo_legacy_product_id');
        $eavSetup->addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [AddLandingPageProductAttributes::class];
    }

    private function getAttributeSetId(EavSetup $eavSetup, string $entityTypeId, string $name): string
    {
        $set = $eavSetup->getAttributeSet($entityTypeId, $name);
        return $set['attribute_set_id'];
    }

    private function getAttributeId(EavSetup $eavSetup, string $entityTypeId, string $code): string
    {
        return $eavSetup->getAttribute($entityTypeId, $code, 'attribute_id');
    }

    private function getAttributeGroupId(
        EavSetup $eavSetup,
        string $entityTypeId,
        string $attributeSetId,
        string $code
    ): string {

        $group = $eavSetup->getAttributeGroupByCode(
            $entityTypeId,
            $attributeSetId,
            $code
        );

        return $group['attribute_group_id'];
    }
}
