<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 17.10.19
 * Time: 13:17
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Edit;


class Shipping extends \MageWorx\OrderEditor\Controller\Adminhtml\Edit\Shipping
{
    public function update()
    {
        $this->updateShippingMethod();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($this->order->getId());
        if($this->order->getOrigData('shipping_amount') != $this->order->getData('shipping_amount')){
            $order->addStatusHistoryComment($this->compareShippingMethod($this->order),$this->order->getStatus());
        }
        $order->save();
    }

    public function updateShippingMethod()
    {
        $params = $this->prepareParams();
        $this->shipping->initParams($params);

        $this->shipping->updateShippingMethod();
    }

    protected function compareShippingMethod($order)
    {
        $shippingComment = '<b>Shipping changes:</b><br>';
        $shippingComment .= '-Shipping method was update from ' . $order->getOrigData('shipping_description') . '-To-' . $order->getData('shipping_description') . '<br>';
        $shippingComment .= '-Shipping total was update from $' . number_format($order->getOrigData('shipping_amount'),2) . '-To-$' . $order->getData('shipping_amount') . '<br>';
        return $shippingComment;
    }


}