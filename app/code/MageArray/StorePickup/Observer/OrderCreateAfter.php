<?php
namespace MageArray\StorePickup\Observer;

class OrderCreateAfter implements \Magento\Framework\Event\ObserverInterface {

    protected $request;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \MageArray\StorePickup\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_objectManager = $objectmanager;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeFactory = $storeFactory;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getOrder();
        $enable = $this->dataHelper->isEnabled();
        if($enable == 1 && $order->getShippingMethod() && $order->getShippingMethod(true)->getCarrierCode() == 'storepickup'){
            $time = \Carbon\Carbon::parse($this->request->getPost('pickup_date'));
            $order->setPickupDate($time->format('Y-m-d'));
            $order->setPickupTime($time->format('h:ia'));
            $order->save();
        }
    }

}