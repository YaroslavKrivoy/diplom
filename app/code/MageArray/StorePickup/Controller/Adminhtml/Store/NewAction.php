<?php

namespace MageArray\StorePickup\Controller\Adminhtml\Store;

class NewAction extends \MageArray\StorePickup\Controller\Adminhtml\Store
{
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
