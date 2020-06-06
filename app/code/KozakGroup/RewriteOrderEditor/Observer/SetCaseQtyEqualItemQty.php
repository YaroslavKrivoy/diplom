<?php

namespace KozakGroup\RewriteOrderEditor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetCaseQtyEqualItemQty implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getData();
        foreach ($data['order']->getItems() as $item)
        if ($item->getCaseQty() == null) {
            $item->setCaseQty($item->getQtyOrdered());
        }
    }
}