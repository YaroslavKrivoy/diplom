<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model\ResourceModel\RuleStatistic;

use Aheadworks\Autorelated\Model\RuleStatistic;
use Aheadworks\Autorelated\Model\ResourceModel\RuleStatistic as RuleStatisticResource;

/**
 * Class Collection
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\RuleStatistic
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(RuleStatistic::class, RuleStatisticResource::class);
    }
}
