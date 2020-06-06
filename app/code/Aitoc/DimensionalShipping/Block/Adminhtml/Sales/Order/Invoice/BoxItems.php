<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\DimensionalShipping\Block\Adminhtml\Sales\Order\Invoice;

/**
 * Aitoc plug-in: Adding box items on the invoice page in admin area
 */
use Aitoc\DimensionalShipping\Helper\Data as DimensionalShippingHelper;
use Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox\CollectionFactory as OrderBoxCollectionFactory;
use Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox\CollectionFactory as OrderItemBoxCollectionFactory;

/**
 * Class BoxItems
 *
 * @package Aitoc\DimensionalShipping\Block\Adminhtml\Sales\Order\Invoice
 */
class BoxItems extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var string - path to template
     */
    protected $_template = 'Aitoc_DimensionalShipping::sales/order/view/orderitems.phtml';
    protected $orderItemBoxRepository;
    protected $orderItemBoxCollectionFactory;
    protected $request;
    protected $orderRepository;
    protected $orderBoxCollectionFactory;
    protected $invoiceRepository;
    protected $shipmentRepository;
    protected $context;

    private $helper;

    /**
     * BoxItems constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Sales\Helper\Admin             $adminHelper
     * @param DimensionalShippingHelper               $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        DimensionalShippingHelper $helper,
        OrderItemBoxCollectionFactory $orderItemBoxCollectionFactory,
        OrderBoxCollectionFactory $orderBoxCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository
    ) {
        parent::__construct($context, $registry, $adminHelper);
        $this->context                       = $context;
        $this->helper                        = $helper;
        $this->request                       = $request;
        $this->orderItemBoxCollectionFactory = $orderItemBoxCollectionFactory;
        $this->orderRepository               = $orderRepository;
        $this->orderBoxCollectionFactory     = $orderBoxCollectionFactory;
        $this->invoiceRepository             = $invoiceRepository;
        $this->shipmentRepository            = $shipmentRepository;
    }


    /**
     * @param $orderBoxId
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getBoxItems($orderBoxId)
    {
        $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create()
            ->addFieldToFilter('order_box_id', $orderBoxId)
            ->addFieldToFilter('not_packed', 0)
            ->addGroupByNameField('sku')
            ->getItems();

        return $orderItemBoxCollection;
    }

    public function getBoxItemQty($sku, $orderBoxId = null)
    {
        $orderId = $this->getOrderId();
        if ($orderBoxId) {
            $orderItemBoxCollectionCount = $this->orderItemBoxCollectionFactory->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('order_box_id', $orderBoxId)
                ->addFieldToFilter('sku', $sku)
                ->count();
        } else {
            $orderItemBoxCollectionCount = $this->orderItemBoxCollectionFactory->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('not_packed', 1)
                ->addFieldToFilter('sku', $sku)
                ->count();
        }

        return $orderItemBoxCollectionCount;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getBoxOrder()
    {
        $orderId            = $this->getOrderId();
        $orderBoxCollection = $this->orderBoxCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
            ->getItems();

        return $orderBoxCollection;
    }

    public function getWeightUnit()
    {
        return $this->context->getScopeConfig()->getValue(
            'general/locale/weight_unit'
        );
    }

    /**
     * @return int|mixed
     */
    public function getOrderId()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $invoiceId = $this->getRequest()->getParam('invoice_id');
            if (!$invoiceId) {
                $shipmentId    = $this->getRequest()->getParam('shipment_id');
                $shipmentModel = $this->shipmentRepository->get($shipmentId);
                $orderId       = $shipmentModel->getOrderId();

                return $orderId;
            }
            $invoiceModel = $this->invoiceRepository->get($invoiceId);
            $orderId      = $invoiceModel->getOrderId();
        }

        return $orderId;
    }

    /**
     * @param $itemId
     *
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     */
    public function getItemById($itemId)
    {
        $orderId = $this->getOrderId();
        $order   = $this->orderRepository->get($orderId);
        $items   = $order->getItems();
        foreach ($items as $item) {
            if ($itemId == $item->getItemId()) {
                return $item;
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function getOrderBoxesList()
    {
        $orderId    = $this->getOrderId();
        $listBoxes  = $this->helper->getBoxListForOrder($orderId);
        $boxesExist = true;
        foreach ($listBoxes as $box) {
            if (!$this->helper->getBoxById($box->getBoxId())) {
                $boxesExist = false;
            }
        }
        if ($boxesExist) {
            return $listBoxes;
        } else {
            return $boxesExist;
        }
    }

    /**
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getAllBoxesList()
    {
        $listBoxes = $this->helper->getBoxList('items');

        return $listBoxes;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getUsedBoxes()
    {
        $orderId                = $this->getOrder()->getId();
        $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
            ->addGroupByNameField('order_box_id')->getItems();

        return $orderItemBoxCollection;
    }

    /**
     * @param $boxId
     *
     * @return int
     */
    public function getBoxQty($boxId)
    {
        $orderId                = $this->getOrderId();
        $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('order_box_id', $boxId);

        return $orderItemBoxCollection->getSize();
    }

    /**
     * @param $idBox
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\BoxInterface|mixed
     */
    public function getBox($idBox)
    {
        $box = $this->helper->getBoxById($idBox);

        return $box;
    }

    /**
     * @return string
     */
    public function getBoxChangeUrl()
    {
        return $this->getUrl('aitdimensionalshipping/orderitems/changebox');
    }

    /**
     * @return array
     */
    public function getNotPackedItemsInOrder()
    {
        $orderId                = $this->getOrderId();
        $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create()
            ->addFieldToFilter('not_packed', 1)
            ->addFieldToFilter('order_id', $orderId)
            ->addGroupByNameField('sku')
            ->getItems();

        return $orderItemBoxCollection;
    }

    /**
     * @return bool
     */
    public function checkInvoice()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            return false;
        } else {
            return true;
        }
    }
}
