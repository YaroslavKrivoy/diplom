<?php

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use KozakGroup\RewriteOrderEditor\Model\Order\Shipment as ShipmentModel;

class Shipment extends Action
{

    protected $orderRepository;

    protected $shipment;

    public $_storeManager;

    /**
     * Shipment constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param ShipmentModel $shipment
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        ShipmentModel $shipment,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->shipment = $shipment;
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id', 0);
        if ($orderId <= 0) {
            echo 'Order not found.';
            return;
        }
        try {
            // load order from database
            $order = $this->orderRepository->get($orderId);
            if ($order == null) {
                echo "Order not loaded from database.";
                return;
            }
            $shipmentPdfFilename = $this->shipment->createShipment($order);
            echo $this->_storeManager->getStore()->getBaseUrl(). 'pub/media/upload/' . $shipmentPdfFilename;
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
        }
    }
}