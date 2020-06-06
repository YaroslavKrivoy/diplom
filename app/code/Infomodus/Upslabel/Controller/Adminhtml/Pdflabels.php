<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Items controller
 */
abstract class Pdflabels extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var object
     */
    protected $collectionFactory;
    protected $shipmentCollectionFactory;
    protected $creditmemoCollectionFactory;
    protected $labelCollectionFactory;
    protected $fileFactory;
    protected $labelCollection;

    protected $pdf;
    protected $conf;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Model\ResourceModel\Items\Collection $labelCollection,
        Filter $filter,
        \Infomodus\Upslabel\Model\ResourceModel\Items\CollectionFactory $labelCollectionFactory,
        OrderFactory $collectionFactory,
        ShipmentFactory $shipmentCollectionFactory,
        CreditmemoFactory $creditmemoCollectionFactory,
        \Infomodus\Upslabel\Helper\Config $conf,
        \Infomodus\Upslabel\Helper\Pdf $pdf,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->conf = $conf;
        $this->pdf = $pdf;
        $this->labelCollection = $labelCollection;
        $this->fileFactory = $fileFactory;
        $this->labelCollectionFactory = $labelCollectionFactory;
    }

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Infomodus_Upslabel::items')->_addBreadcrumb(__('Pdf'), __('Labels'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Infomodus_Upslabel::items');
    }
}
