<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;

class Items extends AbstractAction
{
    /**
     * @return void
     */
    protected function update()
    {
        $this->updateOrderItems();
    }

    /**
     * @return void
     */
    protected function updateOrderItems()
    {
        $params = $this->getRequest()->getParams();

        $order = $this->getOrder();
        $order->editItems($params);
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }
}
