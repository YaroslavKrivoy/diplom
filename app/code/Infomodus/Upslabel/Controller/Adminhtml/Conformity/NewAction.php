<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Conformity;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Conformity
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
