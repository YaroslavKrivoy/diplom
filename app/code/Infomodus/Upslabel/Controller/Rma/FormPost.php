<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Controller\Rma;

class FormPost extends \Infomodus\Upslabel\Controller\Rma
{
    /**
     * Customer address edit action
     *
     * @return \Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        $redirectUrl = null;
        if (!$this->_getFormKeyValidator()->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $products = $this->getRequest()->getParam('product');
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->get('Magento\Sales\Model\Order')->load($orderId);
        $weight = 0;
        foreach ($order->getAllVisibleItems() as $product) {
            if (array_key_exists($product->getId(), $products)
                && is_numeric($products[$product->getId()])
                && $products[$product->getId()] <= $product->getQtyShipped()
            ) {
                $weight += $product->getWeight()*$products[$product->getId()];
            }
        }

        $this->getHandy()->intermediate($order, 'refund');
        $this->getHandy()->defConfParams['package']= [$this->getHandy()->defPackageParams[0]];
        $this->getHandy()->defConfParams['package'][0]['weight'] = $weight;
        $this->getHandy()->getLabel(null, 'refund', null, $this->getHandy()->defConfParams, true);
        if ($this->getHandy()->label[0]->getLstatus()==0) {
            $this->messageManager->addSuccessMessage(__('Label(s) was created'));
        } else {
            $this->messageManager->addErrorMessage(__('Label(s) was not created'));
        }

        if(!$order->getCustomerIsGuest()) {
            return $this->resultRedirectFactory->create()->setUrl($this->_buildUrl('sales/order/view',
                ['order_id' => $orderId]));
        } else {
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }
    }
}
