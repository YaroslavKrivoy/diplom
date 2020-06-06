<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Account;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Account
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
