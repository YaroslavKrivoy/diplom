<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order;

use Magento\Framework\Model\AbstractModel;
use MageWorx\OrderEditor\Model\Config\Source\Shipments\UpdateMode;

/**
 * Class Sales
 */
class Sales extends AbstractModel
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \MageWorx\OrderEditor\Model\Order
     */
    protected $order;

    /**
     * @var \MageWorx\OrderEditor\Model\Invoice
     */
    protected $invoice;

    /**
     * @var \MageWorx\OrderEditor\Model\Creditmemo
     */
    protected $creditmemo;

    /**
     * @var \MageWorx\OrderEditor\Model\Shipment
     */
    protected $shipment;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OrderEditor\Model\Invoice $invoice
     * @param \MageWorx\OrderEditor\Model\Shipment $shipment
     * @param \MageWorx\OrderEditor\Model\Creditmemo $creditmemo
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\OrderEditor\Model\Invoice $invoice,
        \MageWorx\OrderEditor\Model\Shipment $shipment,
        \MageWorx\OrderEditor\Model\Creditmemo $creditmemo,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->invoice            = $invoice;
        $this->creditmemo         = $creditmemo;
        $this->shipment           = $shipment;
        $this->scopeConfig        = $scopeConfig;
        $this->shipmentFactory    = $shipmentFactory;
        $this->shipmentLoader     = $shipmentLoader;
        $this->transactionFactory = $transactionFactory;
        $this->helperData         = $helperData;
        $this->request            = $request;
    }

    /**
     * @param \MageWorx\OrderEditor\Model\Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return \MageWorx\OrderEditor\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function updateSalesObjects()
    {
        try {
            $order = $this->getOrder();

            if (!$order->isTotalWasChanged() && !$order->hasChangesInAmounts()
                && !$order->hasItemsWithIncreasedQty() && !$order->hasAddedItems()
                && !$order->hasItemsWithDecreasedQty() && !$order->hasRemovedItems()
            ) {
                return true;
            }

            if ($order->hasCreditmemos()) {
                $this->updateCreditMemos();
            } else {
                $this->updateInvoices();
            }

            $this->updateShipments();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws \Exception
     * @return void
     */
    protected function updateInvoices()
    {
        if ($this->getOrder()->hasInvoices()) {
            if ($this->isOrderTotalIncreased() && $this->helperData->getIsAllowKeepPrevInvoice()) {
                $this->createInvoiceForOrder();
            } else {
                $this->removeAllInvoices();
                $this->createInvoiceForOrder();
            }
        }
    }

    /**
     * @return bool
     */
    protected function isOrderTotalIncreased()
    {
        $order = $this->getOrder();

        return ($order->hasItemsWithIncreasedQty() || $order->hasAddedItems())
            && (!$order->hasItemsWithDecreasedQty() && !$order->hasRemovedItems());
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateCreditMemos()
    {
        if (!$this->isOrderTotalIncreased() || !$this->helperData->getIsAllowKeepPrevInvoice()) {
            $this->removeAllCreditMemos();
        }
        $this->updateInvoices();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateShipments()
    {
        $order = $this->getOrder();

        if ($order->hasShipments()) {
            switch ($this->helperData->getUpdateShipmentMode()) {
                case UpdateMode::MODE_UPDATE_ADD:
                    if (!$this->isOrderTotalIncreased()) {
                        $this->removeAllShipments();
                    }
                    $this->createShipmentForOrder();
                    break;
                case UpdateMode::MODE_UPDATE_REBUILD:
                    $this->removeAllShipments();
                    $this->createShipmentForOrder();
                    break;
                case UpdateMode::MODE_UPDATE_NOTHING:
                    if ($order->hasRemovedItems()
                        || $order->hasItemsWithDecreasedQty()
                    ) {
                        $this->removeAllShipments();
                    }
                    break;
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function removeAllCreditMemos()
    {
        /**
         * @var \Magento\Sales\Model\Order\Creditmemo $creditMemos
         */
        $creditMemos = $this->getOrder()->getCreditmemosCollection();
        foreach ($creditMemos as $creditMemo) {
            $creditMemo->delete();
        }

        $items = $this->getOrder()->getItems();
        foreach ($items as $item) {
            $item->setQtyRefunded(0)->setQtyReturned(0)
                 ->setDiscountRefunded(0)->setBaseDiscountRefunded(0)
                 ->setAmountRefunded(0)->setBaseAmountRefunded(0)
                 ->setTaxRefunded(0)->setBaseTaxRefunded(0)
                 ->setDiscountTaxCompensationRefunded(0)
                 ->setBaseDiscountTaxCompensationRefunded(0)
                 ->save();
        }

        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;

        $this->getOrder()
             ->setTaxRefunded(0)->setBaseTaxRefunded(0)
             ->setDiscountRefunded(0)->setBaseDiscountRefunded(0)
             ->setSubtotalRefunded(0)->setBaseSubtotalRefunded(0)
             ->setShippingRefunded(0)->setBaseShippingRefunded(0)
             ->setTotalOfflineRefunded(0)->setBaseTotalOfflineRefunded(0)
             ->setTotalRefunded(0)->setBaseTotalRefunded(0)
             ->setState($state)
             ->save();

        $this->getOrder()->getPayment()
             ->setAmountRefunded(0)->setBaseAmountRefunded(0)
             ->setBaseAmountRefundedOnline(0)
             ->setShippingRefunded(0)->setBaseShippingRefunded(0)
             ->save();
    }

    /**
     * @throws \Exception
     */
    protected function removeAllInvoices()
    {
        $invoices = $this->getOrder()->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $shippingMethodDataInRequest = $this->request->getPost('shipping_method');
            if (!$invoice->isCanceled() && !empty($shippingMethodDataInRequest)) {
                $invoice->cancel();
                $transaction = $this->transactionFactory->create();
                $transaction->addObject($invoice->getOrder())
                            ->save();
            }
            $invoice->delete();
        }
        foreach ($this->getOrder()->getAllItems() as $item) {
            $item->setQtyInvoiced(0)->save();
        }

        $this->getOrder()
             ->setTaxInvoiced(0)->setBaseTaxInvoiced(0)
             ->setDiscountInvoiced(0)->setBaseDiscountInvoiced(0)
             ->setSubtotalInvoiced(0)->setBaseSubtotalInvoiced(0)
             ->setTotalInvoiced(0)->setBaseTotalInvoiced(0)
             ->setShippingInvoiced(0)->setBaseShippingInvoiced(0)
             ->setTotalPaid(0)->setBaseTotalPaid(0)
             ->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
             ->save();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function removeAllShipments()
    {
        $shipments = $this->getOrder()->getShipmentsCollection();
        foreach ($shipments as $shipment) {
            $shipment->delete();
        }

        $items = $this->getOrder()->getItems();
        foreach ($items as $item) {
            $item->setQtyShipped(0)->save();
        }

        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;

        $this->getOrder()->setState($state)->save();

        $this->getOrder()->getPayment()
             ->setShippingCaptured(0)->setBaseShippingCaptured(0)
             ->setShippingRefunded(0)->setBaseShippingRefunded(0)
             ->save();
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createInvoiceForOrder()
    {
        $this->getOrder()
             ->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
             ->save();

        if ($this->getOrder()->canInvoice()) {
            $order   = $this->getOrder();
            $invoice = $this->getOrder()->prepareInvoice();
            if (!$invoice) {
                throw new \Exception("Can not create invoice");
            }

            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);

            $invoice->register();

            $transaction = $this->transactionFactory->create();
            $transaction->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();

            /* hack for fix lost $0.01 */
            $order->getPayment()
                  ->setBaseAmountPaid($order->getBaseGrandTotal())
                  ->setAmountPaid($order->getGrandTotal())
                  ->save();
            $order->setBaseTotalPaid($order->getBaseGrandTotal())
                  ->setTotalPaid($order->getGrandTotal())
                  ->save();
        }
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createShipmentForOrder()
    {
        if ($this->getOrder()->canShip()) {
            $this->shipmentLoader->setOrderId($this->getOrder()->getId());
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                throw new \Exception("Can not create shipment");
            }

            $shipment->register();

            $transaction = $this->transactionFactory->create();
            $transaction->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
        }
    }
}
