<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule;

use Aheadworks\Autorelated\Model\Source\Position;
use Aheadworks\Autorelated\Model\Source\Type as SourceType;

/**
 * Class TypeResolver
 *
 * @package Aheadworks\Autorelated\Model\Rule
 */
class TypeResolver
{
    /**
     * @var Position
     */
    private $rulePositionSource;

    /**
     * @param Position $positionSource
     */
    public function __construct(
        Position $positionSource
    ) {
        $this->rulePositionSource = $positionSource;
    }

    /**
     * Get rule type by position rule
     *
     * @param int $position
     * @return int|null
     */
    public function getType($position)
    {
        $productPositions = $this->rulePositionSource->getProductPositions();
        $cartPositions = $this->rulePositionSource->getCartPositions();
        $categoryPositions = $this->rulePositionSource->getCategoryPositions();
        $customPositions = $this->rulePositionSource->getCustomPositions();

        if (false !== array_search($position, $productPositions)) {
            return SourceType::PRODUCT_BLOCK_TYPE;
        }

        if (false !== array_search($position, $cartPositions)) {
            return SourceType::CART_BLOCK_TYPE;
        }

        if (false !== array_search($position, $categoryPositions)) {
            return SourceType::CATEGORY_BLOCK_TYPE;
        }

        if (false !== array_search($position, $customPositions)) {
            return SourceType::CUSTOM_BLOCK_TYPE;
        }

        return null;
    }

    /**
     * Check if rule with specified position uses category related product rule condition
     *
     * @param int $position
     * @return bool
     */
    public function isRulePositionUseCategoryRelatedProductCondition($position)
    {
        $type = $this->getType($position);
        return $this->isRuleTypeUseCategoryRelatedProductCondition($type);
    }

    /**
     * Check if rule with specified type uses category related product rule condition
     *
     * @param int $type
     * @return bool
     */
    public function isRuleTypeUseCategoryRelatedProductCondition($type)
    {
        return ($type == SourceType::CATEGORY_BLOCK_TYPE) || ($type == SourceType::CUSTOM_BLOCK_TYPE);
    }
}
