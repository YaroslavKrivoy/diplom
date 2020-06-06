<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\BlockTitle;

use Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Autorelated\Model\Rule\BlockTitle\StoreValue\ObjectResolver as StoreValueObjectResolver;

/**
 * Class StoreResolver
 *
 * @package Aheadworks\Autorelated\Model\Rule\BlockTitle
 */
class StoreResolver
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var StoreValueObjectResolver
     */
    private $objectResolver;

    /**
     * @param StoreValueObjectResolver $objectResolver
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        StoreValueObjectResolver $objectResolver,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->objectResolver = $objectResolver;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Get value by store ID
     *
     * @param RuleTitleStoreValueInterface[]|array $storeValueItems
     * @param $storeId
     * @return string|null
     */
    public function getValueByStoreId($storeValueItems, $storeId)
    {
        $storeValue = null;

        foreach ($storeValueItems as $storeValueItem) {
            $storeValueObject = $this->objectResolver->resolve($storeValueItem);
            if ($storeValueObject->getStoreId() == $storeId) {
                $storeValue = $storeValueObject->getValue();
                break;
            }
        }
        return $storeValue;
    }
}
