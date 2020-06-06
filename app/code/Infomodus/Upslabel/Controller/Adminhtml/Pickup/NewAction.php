<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pickup;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Pickup
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
