<?php
namespace Webfitters\Wholesale\Controller\Adminhtml\Wholesale;

class Index extends \Magento\Backend\App\Action {

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
         parent::__construct($context);
         $this->resultPageFactory = $resultPageFactory;
    }

    public function execute() {
         return  $resultPage = $this->resultPageFactory->create();
    }
}