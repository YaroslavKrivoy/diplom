<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Duplicator;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Source\Status;

/**
 * Class ObjectBuilder
 * @package Aheadworks\Autorelated\Model\Rule\Duplicator
 */
class ObjectBuilder
{
    /**
     * Build rule duplicate
     *
     * @param RuleInterface $rule
     * @param RuleInterface $duplicateRule
     * @return RuleInterface
     */
    public function build(RuleInterface $rule, RuleInterface $duplicateRule)
    {
        $duplicateRule
            ->setId(null)
            ->setStatus(Status::STATUS_DISABLED);

        if (null === $duplicateRule->getViewedCondition()) {
            $duplicateRule->setViewedCondition('');
        }

        return $duplicateRule;
    }
}
