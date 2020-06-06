<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Stepone extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $order = $this->_objectManager->get('Magento\Sales\Model\Order')
                    ->loadByIncrementId($this->getRequest()->getParam('order_id'));
                $this->_redirect(
                    'infomodus_upslabel/*/edit',
                    ['direction' => $this->getRequest()->getParam('direction'), 'order_id' => $order->getId()]
                );
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                    $this->_redirect('infomodus_upslabel/*/editone');
                return;
            }
        }
        $this->_redirect('infomodus_upslabel/*/');
    }
}
