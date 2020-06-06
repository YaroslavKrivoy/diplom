<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Viewed\Condition;

use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Combine as RuleCombine;
use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Product\AttributesFactory;

/**
 * Class Combine
 *
 * @package Aheadworks\Autorelated\Model\Rule\Viewed\Condition
 */
class Combine extends RuleCombine
{
    /**
     * @var AttributesFactory
     */
    protected $attributeFactory;

    /**
     * @param Context $context
     * @param AttributesFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        AttributesFactory $attributesFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeFactory = $attributesFactory;
        $this->setType(Combine::class);
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => $this->getType(),
                'label' => __('Conditions Combination')
            ],
            $this->attributeFactory->create()->getNewChildSelectOptions(),
        ];

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Return conditions
     *
     * @return array|mixed
     */
    public function getConditions()
    {
        if ($this->getData($this->getPrefix()) === null) {
            $this->setData($this->getPrefix(), []);
        }
        return $this->getData($this->getPrefix());
    }

    /**
     * Collect the valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
