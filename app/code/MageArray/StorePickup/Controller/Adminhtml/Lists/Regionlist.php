<?php

namespace MageArray\StorePickup\Controller\Adminhtml\Lists;

class Regionlist extends \Magento\Framework\App\Action\Action
{
    /**
         * @var \Magento\Framework\View\Result\PageFactory
         */
    protected $resultPageFactory;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Directory\Model\CountryFactory $countryFactory,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory
        ) {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $countrycode = $this->getRequest()->getParam('country');
        $state = "";
        if ($countrycode != '') {
            $statearray =$this->_countryFactory->create()->setId(
                    $countrycode
                )->getLoadedRegionCollection()->toOptionArray();
            if (count($statearray)>0) {
                $state = "<option value=''>--Please Select--</option>";
                foreach ($statearray as $_state) {
                    if ($_state['value']) {
                        $state .= "<option value='" . $_state['label'] . "'>" . $_state['label'] . "</option>";
                    }
                }
            }
        }
        $result['htmlconent']=$state;
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
