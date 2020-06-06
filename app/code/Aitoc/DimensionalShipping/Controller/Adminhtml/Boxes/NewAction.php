<?php

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

class NewAction extends \Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes
{
    /**
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
