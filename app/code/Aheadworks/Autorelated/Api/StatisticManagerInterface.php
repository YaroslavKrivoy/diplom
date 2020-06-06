<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api;

use Aheadworks\Autorelated\Api\Data\RuleStatisticInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Autorelated rule statistic manager interface
 *
 * @api
 */
interface StatisticManagerInterface
{
    /**
     * Update rule views statistic if needed
     *
     * @param int $ruleId
     * @return RuleStatisticInterface|bool
     * @throws NoSuchEntityException
     */
    public function updateRuleViews($ruleId);

    /**
     * Update rule clicks statistic if needed
     *
     * @param int $ruleId
     * @return RuleStatisticInterface|bool
     * @throws NoSuchEntityException
     */
    public function updateRuleClicks($ruleId);
}
