<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel;

/**
 * Class RuleStatistic
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel
 */
class RuleStatistic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_autorelated_profit', 'rule_id');
    }
}
