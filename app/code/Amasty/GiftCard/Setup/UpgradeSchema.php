<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Setup;

use Amasty\GiftCard\Setup\Operation\UpgradeTo170;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeTo170
     */
    private $upgradeTo170;

    public function __construct(
        UpgradeTo170 $upgradeTo170
    ) {
        $this->upgradeTo170 = $upgradeTo170;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.5.1', '<')) {
            $this->updateCodeIdField($installer);
        }

        if (version_compare($context->getVersion(), '1.5.1', '<')) {
            $this->addCustomerCreatedId($setup);
        }

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->upgradeTo170->execute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function updateCodeIdField(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('amasty_amgiftcard_quote');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $setup->getConnection()->addColumn(
                $tableName,
                'code',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'length' => '64k',
                    'comment' => 'Gift Code in Quote'
                ]
            );
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function addCustomerCreatedId(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amgiftcard_account'),
            'customer_created_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 11,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Customer Created Id'
            ]
        );
    }
}
