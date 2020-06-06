<?php

namespace MageArray\StorePickup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function __construct(
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        SchemaSetupInterface $setup,
         ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $setup->getConnection()->addColumn(
                 $setup->getTable('quote'),
                 'pickup_time',
                 [
                     'type' => 'text',
                     'nullable' => false,
                     'comment' => 'Pickup Time',
                 ]
             );

            $setup->getConnection()->addColumn(
                 $setup->getTable('sales_order'),
                 'pickup_time',
                [
                    'type' => 'text',
                    'nullable' => false,
                    'comment' => 'Pickup Time',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magearray_pickup_store'),
                'holiday',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Date of holiday',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magearray_pickup_store'),
                'time_slot',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Time slot',
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magearray_pickup_store'),
                'additional_time_slot',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Additional time slot',
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('sales_order'),
                'pickup_date',
                'pickup_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE
                ]
            );

            $setup->getConnection()->changeColumn(
               $setup->getTable('quote'),
               'pickup_date',
               'pickup_date',
               [
                   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE
               ]
           );
            $setup->getConnection()->addColumn(
                $setup->getTable('magearray_pickup_store'),
                'store_view_ids',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Store Views',
                ]
            );
        }
        $setup->endSetup();
    }
}
