<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 17.10.19
 * Time: 15:13
 */

namespace KozakGroup\RewriteOrderEditor\Model\Order;



class Shipping extends \MageWorx\OrderEditor\Model\Shipping
{


    protected function loadOrder()
    {
        $id = ($this->getOrderId()) ? ($this->getOrderId()) : $_POST['order_id'];
        $this->order->load($id);
    }

    public function recollectShippingAmount()
    {
        $this->loadOrder();
        $this->recollectStandardShippingMethod();
    }
}