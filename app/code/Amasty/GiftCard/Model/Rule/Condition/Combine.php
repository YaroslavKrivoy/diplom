<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Rule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->setType(\Magento\SalesRule\Model\Rule\Condition\Combine::class);
    }

    public function getNewChildSelectOptions()
    {
        $conditions = [['value' => '', 'label' => __('Please choose a condition to add.')]];
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Magento\SalesRule\Model\Rule\Condition\Product\Found::class,
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => \Magento\SalesRule\Model\Rule\Condition\Product\Subselect::class,
                    'label' => __('Products subselection')
                ],
                [
                    'value' => \Amasty\GiftCard\Model\Rule\Condition\Combine::class,
                    'label' => __('Conditions combination')
                ]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
