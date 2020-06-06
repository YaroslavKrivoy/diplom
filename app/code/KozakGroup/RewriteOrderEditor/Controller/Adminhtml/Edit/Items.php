<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 02.10.19
 * Time: 11:31
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Edit;


class Items extends \MageWorx\OrderEditor\Controller\Adminhtml\Edit\Items
{
    public function update()
    {
        $this->updateOrderItems();
        $this->prepareObjects();
        $this->updateShippingInfo();
        if($this->order->getOrigData('shipping_amount') != $this->order->getData('shipping_amount')){
            $this->order->addStatusHistoryComment($this->compareShippingMethod($this->order),$this->order->getStatus());
        }
        $this->recalculateTotals();
        $this->order->save();
    }

    protected function compareShippingMethod($order)
    {
        $shippingComment = '<b>Shipping & Handling changes:</b><br>';
        $shippingComment .= '-Shipping & Handling was update from $' . number_format($order->getOrigData('shipping_amount'),2) . '-To-$' . $order->getData('shipping_amount') . '<br>';
        return $shippingComment;
    }
}