<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 02.10.19
 * Time: 14:16
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Edit;


class Payment extends \MageWorx\OrderEditor\Controller\Adminhtml\Edit\Payment
{

    public function update()
    {
        $this->updatePaymentMethod();
    }

}