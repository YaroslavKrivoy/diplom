<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Bulk extends \Infomodus\Upslabel\Controller\Adminhtml\Items\AbstractMassAction
{
    protected $_conf;
    protected $collectionFactory;
    protected $shipmentCollectionFactory;
    protected $creditmemoCollectionFactory;
    protected $labelCollectionFactory;
    protected $fileFactory;
    protected $orderRepository;
    protected $handyFactory;

    /**
     * Bulk constructor.
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory
     * @param \Infomodus\Upslabel\Model\ResourceModel\Items\CollectionFactory $labelCollectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Infomodus\Upslabel\Helper\Config $conf
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Infomodus\Upslabel\Helper\HandyFactory $handyFactory
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory,
        \Infomodus\Upslabel\Model\ResourceModel\Items\CollectionFactory $labelCollectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Infomodus\Upslabel\Helper\Config $conf,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Infomodus\Upslabel\Helper\HandyFactory $handyFactory
    )
    {
        $this->_conf = $conf;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->orderRepository = $orderRepository;
        $this->handyFactory = $handyFactory;
        parent::__construct($context, $filter);
    }

    protected function massAction(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        $ids = $collection->getAllIds();
        if (count($ids) > 0) {
            $isOrder = false;
            switch ($this->getRequest()->getParam('namespace')) {
                case 'sales_order_grid':
                    $type = 'shipment';
                    $isOrder = true;
                    $errorLink = 'sales/order';
                    break;
                case 'sales_order_shipment_grid':
                    $isOrder = true;
                    $type = 'shipment';
                    $errorLink = 'sales/shipment';
                    break;
                case 'sales_order_creditmemo_grid':
                    $type = 'refund';
                    $errorLink = 'sales/creditmemo';
                    break;
                default:
                    $type = 'shipment';
                    $errorLink = 'infomodus_upslabel/items';
                    break;
            }
            $isOk = false;
            foreach ($ids as $id) {
                $handy = $this->handyFactory->create();
                if ($isOrder === true) {
                    $order = $this->orderRepository->get($id);
                    $storeId = $order->getStoreId();
                    $isShippingActiveMethods = $this->_conf
                        ->getStoreConfig('upslabel/bulk_create_labels/bulk_shipping_methods', $storeId);
                    if ($isShippingActiveMethods == 'specify') {
                        $shippingActiveMethods = trim($this->_conf
                            ->getStoreConfig('upslabel/bulk_create_labels/apply_to', $storeId), " ,");
                        $shippingActiveMethods = strlen($shippingActiveMethods) > 0 ?
                            explode(",", $shippingActiveMethods) : [];
                    }
                    $isOrderStatuses = $this->_conf
                        ->getStoreConfig('upslabel/bulk_create_labels/bulk_order_status', $storeId);
                    if ($isOrderStatuses == 'specify') {
                        $orderStatuses = explode(",", trim($this->_conf
                            ->getStoreConfig('upslabel/bulk_create_labels/orderstatus', $storeId),
                            " ,"));
                    }
                    if ((
                            $isShippingActiveMethods == 'all'
                            || (
                                isset($shippingActiveMethods)
                                && !empty($shippingActiveMethods)
                                && in_array($order->getShippingMethod(), $shippingActiveMethods)
                            )
                        )
                        &&
                        (
                            $isOrderStatuses
                            ||
                            (
                                isset($orderStatuses)
                                && !empty($orderStatuses)
                                && in_array($order->getStatus(), $orderStatuses)
                            )
                        )
                    ) {
                        $handy->intermediate($id, $type);
                        $handy->defConfParams['package'] = $handy->defPackageParams;
                        $handy->getLabel(null, $type, null, $handy->defConfParams);
                    }
                } else {
                    $handy->intermediate(null, $type, $id);
                    $handy->defConfParams['package'] = $handy->defPackageParams;
                    $handy->getLabel(null, $type, $id, $handy->defConfParams);
                }

                if (count($handy->error) == 0) {
                    $isOk = true;
                } else {
                    $this->messageManager->addErrorMessage(__('For the selected items are not created labels.'));
                }

            }

            if ($isOk === true) {
                $this->messageManager->addSuccessMessage(__('For the selected items are created labels.'));
            }

            return $this->resultRedirectFactory->create()->setPath($errorLink . '/');
        }
    }
}
