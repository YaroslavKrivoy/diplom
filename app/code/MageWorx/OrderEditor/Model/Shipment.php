<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Magento\Framework\App\ResourceConnection;

class Shipment extends \Magento\Sales\Model\Order\Shipment
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
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\Order\Shipment\CommentFactory $commentFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory $commentCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
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
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\Order\Shipment\CommentFactory $commentFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Comment\CollectionFactory $commentCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
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
            $shipmentItemCollectionFactory,
            $trackCollectionFactory,
            $commentFactory,
            $commentCollectionFactory,
            $orderRepository,
            $resource,
            $resourceCollection,
            $data
        );

        $this->scopeConfig = $scopeConfig;
        $this->appResourceConnection = $appResourceConnection;
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $collection = $this->_commentCollectionFactory->create()
            ->setShipmentFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }

        $collection = $this->_shipmentItemCollectionFactory->create()
            ->setShipmentFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }

        $collection = $this->_trackCollectionFactory->create()
            ->setShipmentFilter($this->getId());
        foreach ($collection as $object) {
            $object->delete();
        }

        $this->deleteFromGrid();

        return parent::beforeDelete();
    }

    /**
     * Delete From Grid
     *
     * @return void
     */
    protected function deleteFromGrid()
    {
        $id = $this->getId();
        if (!empty($id)) {
            $connection = $this->appResourceConnection->getConnection(
                \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
            );
            $gridTable = $connection->getTableName('sales_shipment_grid');
            $connection->delete($gridTable, ['entity_id = (?)' => $id]);
        }
    }

    /**
     * @return bool
     */
    public function isAllowDeleteShipment()
    {
        return true;
//        return $this->scopeConfig->getValue('mageworx_order_management/order_editor/allow_delete/shipments');
    }

    /**
     * @return void
     */
    public function deleteShipment()
    {
        $order = $this->getOrder();

        $this->cancel();
        $this->delete();
    }

    /**
     * @return void
     */
    public function cancel()
    {
        $this->cancelItems();
        $this->changeOrderStatusAfterDeleteShipment();
    }

    /**
     * @return void
     */
    protected function cancelItems()
    {
        $shipmentItems = $this->getItemsCollection();

        /**
         * @var \Magento\Sales\Model\Order\Shipment\Item $shipmentItem
         */
        foreach ($shipmentItems as $shipmentItem) {
            $orderItems = $this->getOrder()->getItems();
            foreach ($orderItems as $orderItem) {
                if ($orderItem->getProductId() != $shipmentItem->getProductId()) {
                    continue;
                }

                $qty = $orderItem->getQtyShipped() - $shipmentItem->getQty();
                $orderItem->setQtyShipped($qty)->save();
            }
        }
    }

    /**
     * @return void
     */
    protected function changeOrderStatusAfterDeleteShipment()
    {
        $order = $this->getOrder();

        $state = ($order->hasInvoices())
            ? \Magento\Sales\Model\Order::STATE_PROCESSING
            : \Magento\Sales\Model\Order::STATE_NEW;

        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));

        $order->save();
    }
}
