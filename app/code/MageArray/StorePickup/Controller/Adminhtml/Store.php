<?php
namespace MageArray\StorePickup\Controller\Adminhtml;

abstract class Store extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Escaper $escaper,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeFactory = $storeFactory;
        $this->backendSession = $context->getSession();
        $this->resultForwardFactory = $resultForwardFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('MageArray_StorePickup::store');
    }
}
