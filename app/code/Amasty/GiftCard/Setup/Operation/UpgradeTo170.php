<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeTo170
 *
 * Add column for Code Set Conditions
 */
class UpgradeTo170
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amgiftcard_code_set'),
            'conditions_serialized',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Conditions'
            ]
        );
    }
}
