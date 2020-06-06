<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api\Data;

/**
 * Autorelated rule statistic interface
 *
 * @api
 */
interface RuleStatisticInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'rule_id';
    const VIEW_COUNT = 'view_count';
    const CLICK_COUNT = 'click_count';
    /**#@-*/

    /**
     * Get rule ID
     *
     * @return int|null
     */
    public function getRuleId();

    /**
     * Get view count
     *
     * @return int
     */
    public function getViewCount();

    /**
     * Get click count
     *
     * @return int
     */
    public function getClickCount();

    /**
     * Set rule ID
     *
     * @param int $id
     * @return RuleStatisticInterface
     */
    public function setRuleId($id);

    /**
     * Set view count
     *
     * @param int $viewCount
     * @return RuleStatisticInterface
     */
    public function setViewCount($viewCount);

    /**
     * Set click count
     *
     * @param int $clickCount
     * @return RuleStatisticInterface
     */
    public function setClickCount($clickCount);
}
