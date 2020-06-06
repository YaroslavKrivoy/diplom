<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\AttributeValueFactory;

class Invoice extends \Magento\Sales\Model\Order\Invoice
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
     * @var \MageWorx\OrderEditor\Model\Creditmemo
     */
    protected $creditmemo;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\Order\Invoice\Config $invoiceConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory $invoiceItemCollectionFactory
     * @param \Magento\Sales\Model\Order\Invoice\CommentFactory $invoiceCommentFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\Comment\CollectionFactory $commentCollectionFactory
     * @param ResourceConnection $appResourceConnection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \MageWorx\OrderEditor\Model\Creditmemo $creditmemo
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\Order\Invoice\Config $invoiceConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory $invoiceItemCollectionFactory,
        \Magento\Sales\Model\Order\Invoice\CommentFactory $invoiceCommentFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\Comment\CollectionFactory $commentCollectionFactory,
        ResourceConnection $appResourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \MageWorx\OrderEditor\Model\Creditmemo $creditmemo,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $invoiceConfig,
            $orderFactory,
            $calculatorFactory,
            $invoiceItemCollectionFactory,
            $invoiceCommentFactory,
            $commentCollectionFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->scopeConfig = $scopeConfig;
        $this->creditmemo = $creditmemo;
        $this->appResourceConnection = $appResourceConnection;
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $this->deleteComments();
        $this->deleteInvoiceItems();
        $this->deleteFromGrid();

        return parent::beforeDelete();
    }

    /**
     * @return void
     */
    protected function deleteComments()
    {
        $collection = $this->_commentCollectionFactory->create()
            ->setInvoiceFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }
    }

    /**
     * @return void
     */
    protected function deleteInvoiceItems()
    {
        $collection = $this->_invoiceItemCollectionFactory->create()
            ->setInvoiceFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }
    }

    /**
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = $this->getId();
        if (!empty($id)) {
            $connection = $this->appResourceConnection
                ->getConnection(ResourceConnection::DEFAULT_CONNECTION);

            $salesInvoiceGridTable = $connection->getTableName('sales_invoice_grid');
            $connection->delete($salesInvoiceGridTable, ['entity_id = (?)' => $id]);
        }
    }

    /**
     * @return bool
     */
    public function isAllowDeleteInvoice()
    {
        return true;
        //return $this->scopeConfig->getValue('mageworx_order_management/order_editor/allow_delete/invoices');
    }

    /**
     * @return void
     */
    public function deleteInvoice()
    {
        $order = $this->getOrder();
        $this->deleteRelatedCreditMemos();
        $this->cancelInvoice();
        $this->delete();
    }

    /**
     * @return void
     */
    protected function cancelInvoice()
    {
        try {
            if (!$this->isCanceled()) {
                $this->cancel()->save()->getOrder()->save();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function deleteRelatedCreditMemos()
    {
        $creditMemos = $this->getOrder()->getCreditmemosCollection();
        foreach ($creditMemos as $creditMemo) {
            $creditMemo = $this->creditmemo->load($creditMemo->getEntityId());
            $creditMemo->cancel();
            $creditMemo->delete();
        }
    }
}
