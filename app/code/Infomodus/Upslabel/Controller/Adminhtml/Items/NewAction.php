<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class NewAction extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('editone');
    }
}
