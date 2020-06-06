<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Config\Source\Order;

class State implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $states = $this->getStates();

        $options = [];
        foreach ($states as $state) {
            $options[] = ['value' => $state, 'label' => ucfirst($state)];
        }
        return $options;
    }

    /**
     * Return array of states
     *
     * @return array
     */
    private function getStates()
    {
        return [
            \Magento\Sales\Model\Order::STATE_NEW,
            \Magento\Sales\Model\Order::STATE_PROCESSING,
            \Magento\Sales\Model\Order::STATE_COMPLETE,
            \Magento\Sales\Model\Order::STATE_CLOSED,
            \Magento\Sales\Model\Order::STATE_CANCELED,
            \Magento\Sales\Model\Order::STATE_HOLDED,
        ];
    }
}
