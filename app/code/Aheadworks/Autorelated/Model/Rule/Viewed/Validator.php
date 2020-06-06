<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Viewed;

use Aheadworks\Autorelated\Model\Rule\CurrentPageObject;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Validator as ConditionValidator;

/**
 * Class Validator
 *
 * @package Aheadworks\Autorelated\Model\Rule\Viewed
 */
class Validator
{
    /**
     * @var CurrentPageObject
     */
    private $currentPageObject;

    /**
     * @var ConditionValidator
     */
    private $conditionValidator;

    /**
     * @param CurrentPageObject $currentPageObject
     * @param ConditionValidator $conditionValidator
     */
    public function __construct(
        CurrentPageObject $currentPageObject,
        ConditionValidator $conditionValidator
    ) {
        $this->currentPageObject = $currentPageObject;
        $this->conditionValidator = $conditionValidator;
    }

    /**
     * Is show ARP block
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    public function canShow($rule, $blockType)
    {
        if ($rule->getType() == Type::CUSTOM_BLOCK_TYPE) {
            return true;
        }

        if ($rule->getType() == Type::CATEGORY_BLOCK_TYPE) {
            return $this->canShowOnCategoryPage($rule, $blockType);
        }

        return $this->canShowOnProductAndCartPage($rule, $blockType);
    }

    /**
     * Can show ARP block on the category page
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function canShowOnCategoryPage($rule, $blockType)
    {
        $currentCategoryId = $this->currentPageObject->getCurrentCategoryIdForBlock($rule, $blockType);
        if ($currentCategoryId
            && (!$rule->getCategoryIds() || in_array($currentCategoryId, explode(',', $rule->getCategoryIds())))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Can show ARP block on the product and cart page
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function canShowOnProductAndCartPage($rule, $blockType)
    {
        $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($rule, $blockType);
        if (!$currentProductId) {
            return false;
        }

        return $this->conditionValidator->isProductValid($rule, $currentProductId);
    }
}
