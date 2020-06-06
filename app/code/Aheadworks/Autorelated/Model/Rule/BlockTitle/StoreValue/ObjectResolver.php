<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\BlockTitle\StoreValue;

use Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterface;
use Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ObjectResolver
 *
 * @package Aheadworks\Autorelated\Model\Rule\RuleTitle\StoreValue
 */
class ObjectResolver
{
    /**
     * @var RuleTitleStoreValueInterfaceFactory
     */
    private $storeValueFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param RuleTitleStoreValueInterfaceFactory $storeValueFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        RuleTitleStoreValueInterfaceFactory $storeValueFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->storeValueFactory = $storeValueFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve row storeValue
     *
     * @param RuleTitleStoreValueInterface[]|array $storeValueItem
     * @return RuleTitleStoreValueInterface
     */
    public function resolve($storeValueItem)
    {
        if ($storeValueItem instanceof RuleTitleStoreValueInterface) {
            $storeValueObject = $storeValueItem;
        } else {
            /** @var RuleTitleStoreValueInterface $labelObject */
            $storeValueObject = $this->storeValueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $storeValueObject,
                $storeValueItem,
                RuleTitleStoreValueInterface::class
            );
        }
        return $storeValueObject;
    }
}
