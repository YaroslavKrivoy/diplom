<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\NewItem;

class Form extends \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Grid
{
    /**
     * @var \Magento\Sales\Model\Order\Item[]
     */
    protected $newOrderItems;

    /**
     * @var []
     */
    protected $errors = [];

    /**
     * @param \Magento\Sales\Model\Order\Item[] $newOrderItems
     * @return $this
     */
    public function setNewOrderItems($newOrderItems)
    {
        $this->newOrderItems = $newOrderItems;
        return $this;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getNewOrderItems()
    {
        return $this->newOrderItems;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getItems()
    {
        return $this->getNewOrderItems();
    }

    /**
     * @param string[] $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
