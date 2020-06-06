<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Address;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Address
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
