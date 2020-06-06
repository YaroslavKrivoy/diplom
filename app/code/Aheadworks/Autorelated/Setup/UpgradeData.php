<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Setup;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Serialize\SerializeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Aheadworks\Autorelated\Model\Serialize\Factory as SerializeFactory;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Autorelated\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param SerializeFactory $serializeFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        SerializeFactory $serializeFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->serializer = $serializeFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<=')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'aw_arp_override_native',
                [
                    'group' => '',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Override Automatic Related Products',
                    'type' => 'int',
                    'input' => 'boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => true,
                    'sort_order' => '1'
                ]
            );
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.4.1', '<')) {
            $this->convertSerializedConditionsToJson($setup);
        }

        $setup->endSetup();
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function convertSerializedConditionsToJson($setup)
    {
        $connection = $setup->getConnection();
        $select = $connection->select()->from(
            $setup->getTable('aw_autorelated_rule'),
            [
                RuleInterface::ID,
                RuleInterface::VIEWED_CONDITION,
                RuleInterface::PRODUCT_CONDITION
            ]
        );
        $rulesConditions = $connection->fetchAssoc($select);
        foreach ($rulesConditions as $ruleConditions) {
            $unsrViewedCond = $this->unserialize($ruleConditions[RuleInterface::VIEWED_CONDITION]);
            $unsrProductCond = $this->unserialize($ruleConditions[RuleInterface::PRODUCT_CONDITION]);
            if ($unsrViewedCond !== false && $unsrProductCond !== false) {
                $ruleConditions[RuleInterface::VIEWED_CONDITION] = empty($unsrViewedCond)
                    ? ''
                    : $this->serializer->serialize($unsrViewedCond);
                $ruleConditions[RuleInterface::PRODUCT_CONDITION] = empty($unsrProductCond)
                    ? ''
                    : $this->serializer->serialize($unsrProductCond);

                $connection->update(
                    $setup->getTable('aw_autorelated_rule'),
                    [
                        RuleInterface::VIEWED_CONDITION => $ruleConditions[RuleInterface::VIEWED_CONDITION],
                        RuleInterface::PRODUCT_CONDITION => $ruleConditions[RuleInterface::PRODUCT_CONDITION]
                    ],
                    RuleInterface::ID . ' = ' . $ruleConditions[RuleInterface::ID]
                );
            }
        }
    }

    /**
     * Unserialize string with unserialize method
     *
     * @param $string
     * @return array|bool
     */
    private function unserialize($string)
    {
        $result = '';
        if (!empty($string)) {
            $result = @unserialize($string);
            if ($result !== false || $string === 'b:0;') {
            } else {
                $result = false;
            }
        }
        return $result;
    }
}
