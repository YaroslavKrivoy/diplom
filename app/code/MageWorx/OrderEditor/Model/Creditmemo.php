<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Creditmemo extends \Magento\Sales\Model\Order\Creditmemo
{
    /**
     * @var ResourceConnection
     */
    protected $appResourceConnection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\Order\Creditmemo\Config $creditmemoConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\CollectionFactory $cmItemCollectionFactory
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Creditmemo\CommentFactory $commentFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\CollectionFactory $commentCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param ResourceConnection $appResourceConnection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\Order\Creditmemo\Config $creditmemoConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\CollectionFactory $cmItemCollectionFactory,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Creditmemo\CommentFactory $commentFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Comment\CollectionFactory $commentCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        ResourceConnection $appResourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $creditmemoConfig,
            $orderFactory,
            $cmItemCollectionFactory,
            $calculatorFactory,
            $storeManager,
            $commentFactory,
            $commentCollectionFactory,
            $priceCurrency,
            $resource,
            $resourceCollection,
            $data
        );

        $this->scopeConfig = $scopeConfig;
        $this->appResourceConnection = $appResourceConnection;
    }

    /**
     * @return void
     */
    public function cancel()
    {
        $this->cancelItems();
        $this->updateOrderStatusAfterCancel();
        $this->cancelOrderTotal();
    }

    /**
     * @return void
     */
    protected function cancelItems()
    {
        $creditmemoItems = $this->getItemsCollection();

        /** @var $creditmemoItem \Magento\Sales\Model\Order\Creditmemo\Item */
        foreach ($creditmemoItems as $creditmemoItem) {
            $orderItems = $this->getOrder()->getItems();

            foreach ($orderItems as $orderItem) {
                if ($orderItem->getProductId() != $creditmemoItem->getProductId()) {
                    continue;
                }

                $amountRefunded        = $orderItem->getAmountRefunded() - $creditmemoItem->getRowTotal();
                $baseAmountRefunded    = $orderItem->getBaseAmountRefunded() - $creditmemoItem->getRowTotal();
                $taxRefunded           = $orderItem->getTaxRefunded() - $creditmemoItem->getTaxAmount();
                $baseTaxRefunded       = $orderItem->getBaseTaxRefunded() - $creditmemoItem->getBaseTaxAmount();
                $discountRefunded      = $orderItem->getDiscountRefunded() - $creditmemoItem->getDiscountAmount();
                $baseDiscountRefunded  = $orderItem->getBaseDiscountRefunded() - $creditmemoItem->getBaseDiscountAmount();
                $hiddenTaxRefunded     = $orderItem->getDiscountTaxCompensationRefunded() - $creditmemoItem->getDiscountTaxCompensationAmount();
                $baseHiddenTaxRefunded = $orderItem->getBaseDiscountTaxCompensationRefunded() - $creditmemoItem->getBaseDiscountTaxCompensationAmount();
                $qtyRefunded = $orderItem->getQtyRefunded() - $creditmemoItem->getQty();

                if ($amountRefunded >= 0) {
                    $orderItem->setAmountRefunded($amountRefunded);
                }
                if ($baseAmountRefunded >= 0) {
                    $orderItem->setBaseAmountRefunded($baseAmountRefunded);
                }
                if ($taxRefunded >= 0) {
                    $orderItem->setTaxRefunded($taxRefunded);
                }
                if ($baseTaxRefunded >= 0) {
                    $orderItem->setBaseTaxRefunded($baseTaxRefunded);
                }
                if ($discountRefunded >= 0) {
                    $orderItem->setDiscountRefunded($discountRefunded);
                }
                if ($baseDiscountRefunded >= 0) {
                    $orderItem->setBaseDiscountRefunded($baseDiscountRefunded);
                }
                if ($hiddenTaxRefunded >= 0) {
                    $orderItem->setDiscountTaxCompensationRefunded($hiddenTaxRefunded);
                }
                if ($baseHiddenTaxRefunded >= 0) {
                    $orderItem->setBaseDiscountTaxCompensationRefunded($baseHiddenTaxRefunded);
                }
                if ($qtyRefunded >= 0) {
                    $orderItem->setQtyRefunded($qtyRefunded);
                }

                $orderItem->save();
            }
        }
    }

    /**
     * @return void
     */
    protected function cancelOrderTotal()
    {
        $order = $this->getOrder();
        $totalRefunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $baseTotalRefunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $order->setTotalRefunded($totalRefunded);
        $order->setBaseTotalRefunded($baseTotalRefunded);
        $order->save();
    }

    /**
     * @return void
     */
    protected function updateOrderStatusAfterCancel()
    {
        $order = $this->getOrder();

        if ($order->hasInvoices() && $order->hasShipments()) {
            $state = \Magento\Sales\Model\Order::STATE_COMPLETE;
        } else if ($order->hasInvoices()) {
            $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
        } else {
            $state = $order->getState();
        }
        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $collection = $this->_commentCollectionFactory->create()->setCreditmemoFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }

        $collection = $this->_cmItemCollectionFactory->create()->setCreditmemoFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }

        $this->deleteFromGrid();

        return parent::beforeDelete();
    }

    /**
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = $this->getId();
        if (!empty($id)) {
            $connection = $this->appResourceConnection
                ->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $salesCreditMemosGridTable = $connection->getTableName('sales_creditmemo_grid');
            $connection->delete($salesCreditMemosGridTable, ['entity_id = (?)' => $id]);
        }
    }

    /**
     * @return void
     */
    public function deleteCreditmemo()
    {
        $order = $this->getOrder();

        $this->cancel();
        $this->delete();
    }

    /**
     * @return bool
     */
    public function isAllowDeleteCreditmemo()
    {
        return true;
        //return $this->scopeConfig->getValue('mageworx_order_management/order_editor/allow_delete/credit_memos');
    }
}
