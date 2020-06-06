<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model;

use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Registry for \Aheadworks\Autorelated\Api\Data\RuleInterface
 *
 * @package Aheadworks\Autorelated\Model
 */
class RuleRegistry
{
    /**
     * Retrieve Rule from registry
     *
     * @param int $ruleId
     * @return RuleInterface|null
     */
    public function retrieve($ruleId)
    {
        if (!isset($this->ruleRegistry[$ruleId])) {
            return null;
        }
        return $this->ruleRegistry[$ruleId];
    }

    /**
     * Remove instance of the Rule from registry
     *
     * @param int $ruleId
     * @return void
     */
    public function remove($ruleId)
    {
        if (isset($this->ruleRegistry[$ruleId])) {
            unset($this->ruleRegistry[$ruleId]);
        }
    }

    /**
     * Replace existing Rule with a new one
     *
     * @param RuleInterface $rule
     * @return $this
     */
    public function push(RuleInterface $rule)
    {
        if ($ruleId = $rule->getId()) {
            $this->ruleRegistry[$ruleId] = $rule;
        }
        return $this;
    }
}
