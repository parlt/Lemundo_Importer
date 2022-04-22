<?php
declare(strict_types=1);

namespace Lemundo\Importer\Setup\Patch\Data;

use Lemundo\Importer\Config\DefaultConfig;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Setup\Exception;

class AddLandingPageProductAttributeSet implements DataPatchInterface, PatchRevertableInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

    private AttributeSetFactory $attributeSetFactory;

    private DefaultConfig $config;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        DefaultConfig $config
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->config = $config;
    }

    public static function getDependencies()
    {
        return [];
    }

    /**
     * @throws Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);

        $attributeSetName = $this->config->getLandingPageAttributeSetName();
        if (empty($attributeSetName)) {
            throw new Exception('attribute set name can not be empty, please check your config.');
        }

        $data = [
            'attribute_set_name' => $attributeSetName,
            'entity_type_id' => $entityTypeId,
            'sort_order' => 200,
        ];

        try {
            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($attributeSetId);
            $attributeSet->save();

        } catch (\Exception $exception) {
            // if somebody has created a attribute set with same name
            // nothing to do
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function getAliases()
    {
        return [];
    }
}
