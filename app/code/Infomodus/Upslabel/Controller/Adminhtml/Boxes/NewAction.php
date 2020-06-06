<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Boxes;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Boxes
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
