<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\Autorelated\Api\Data\RuleStatisticInterface;
use Aheadworks\Autorelated\Model\ResourceModel\RuleStatistic as ResourceRuleStatistic;

/**
 * Class RuleStatistic
 *
 * @package Aheadworks\Autorelated\Model
 */
class RuleStatistic extends AbstractModel implements RuleStatisticInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceRuleStatistic::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCount()
    {
        return $this->getData(self::VIEW_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getClickCount()
    {
        return $this->getData(self::CLICK_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewCount($viewCount)
    {
        return $this->setData(self::VIEW_COUNT, $viewCount);
    }

    /**
     * {@inheritdoc}
     */
    public function setClickCount($clickCount)
    {
        return $this->setData(self::CLICK_COUNT, $clickCount);
    }
}
