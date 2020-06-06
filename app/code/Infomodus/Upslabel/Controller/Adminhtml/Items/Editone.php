<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Editone extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_searchCriteria;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute()
    {
        $this->_initAction();
        $this->_view->getLayout()->getBlock('items_items_editone');
        $this->_view->renderLayout();
    }
}
