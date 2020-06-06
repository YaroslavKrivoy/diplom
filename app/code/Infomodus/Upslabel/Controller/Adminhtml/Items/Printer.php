<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Printer extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_handy;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_handy = $handy;
    }

    public function execute()
    {
        $imname = $this->getRequest()->getParam('imname');
        $path_url = $this->_handy->_conf->getBaseUrl('media') . '/upslabel/label/';
        $content = '<html>
            <head>
            <title>Print Shipping Label</title>
            </head>
            <body>
            <img src="' . $path_url . $imname . '" />
            <script>
            window.onload = function(){window.print();}
            </script>
            </body>
            </html>';
        $this->getResponse()
            ->setContent($content);
        return;
    }
}
