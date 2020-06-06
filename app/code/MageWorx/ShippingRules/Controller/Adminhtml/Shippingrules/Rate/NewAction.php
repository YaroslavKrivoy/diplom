<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

class NewAction extends \MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
