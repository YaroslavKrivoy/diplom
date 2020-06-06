<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Setup;

use Amasty\GiftCard\Model\Product\Type\GiftCard;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($this->isVersionGrater($context, '1.0.5')) {
            $this->addAttributeToGift($eavSetup, 'weight');
        }

        if ($this->isVersionGrater($context, '1.1.0')) {
            $this->addGiftCardType($eavSetup);
        }

        if ($this->isVersionGrater($context, '1.4.1')) {
            $this->addAttributeToGift($eavSetup, 'tax_class_id');
        }

        if ($this->isVersionGrater($context, '1.5.1')) {
            $this->deleteAllowedAttribute($eavSetup);
        }

        $setup->endSetup();
    }

    /**
     * @param EavSetup $eavSetup
     * @param $attribute
     */
    private function addAttributeToGift(EavSetup $eavSetup, $attribute)
    {
        // make 'weight' attribute applicable to gift card products
        $applyTo = $eavSetup->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $attribute, 'apply_to');
        if ($applyTo) {
            $applyTo = explode(',', $applyTo);
            if (!in_array(GiftCard::TYPE_GIFTCARD_PRODUCT, $applyTo)) {
                $applyTo[] = GiftCard::TYPE_GIFTCARD_PRODUCT;
                $eavSetup->updateAttribute(
                    ProductAttributeInterface::ENTITY_TYPE_CODE,
                    $attribute,
                    'apply_to',
                    join(',', $applyTo)
                );
            }
        }
    }

    /**
     * @param EavSetup $eavSetup
     */
    private function addGiftCardType(EavSetup $eavSetup)
    {
        $attributeGroupName = 'Gift Card Information';
        $entityType = ProductAttributeInterface::ENTITY_TYPE_CODE;
        $eavSetup->addAttributeGroup($entityType, 'Default', $attributeGroupName, 9);

        $eavSetup->addAttribute(
            $entityType,
            'am_gift_card_type',
            [
                'type' => 'int',
                'label' => '',
                'backend' => '',
                'input' => '',
                'source' => '',
                'required' => false,
                'sort_order' => -30,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => '',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'apply_to' => 'amgiftcard',
                'class' => 'validate-number',
                'visible' => false,
                'used_in_product_listing' => true,
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    private function deleteAllowedAttribute(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, 'am_allow_message');
    }

    /**
     * @param $version
     * @param ModuleContextInterface $context
     * @return mixed
     */
    private function isVersionGrater(ModuleContextInterface $context, $version)
    {
        return version_compare($context->getVersion(), $version, '<');
    }
}
