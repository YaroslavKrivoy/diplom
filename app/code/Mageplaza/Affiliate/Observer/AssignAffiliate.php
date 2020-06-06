<?php
namespace Mageplaza\Affiliate\Observer;

class AssignAffiliate implements \Magento\Framework\Event\ObserverInterface {

    protected $order;
    protected $request;
    protected $account;
    protected $customer;
    protected $helper;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\App\RequestInterface $request,
        \Mageplaza\Affiliate\Model\AccountFactory $account,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Mageplaza\Affiliate\Helper\Calculation\Commission $helper
    ) {
        $this->order = $order;
        $this->request = $request;
        $this->account = $account;
        $this->customer = $customer;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $request = $this->request->getParam('order');
        if(isset($request['affiliate_id']) && $request['affiliate_id'] > 0){
            $order = $observer->getOrder();
            $account = $this->account->create()->load($request['affiliate_id']);
            $customer = $this->customer->create()->load($account->getCustomerId());
            $commission = $this->helper->adminCollect($account, $order->getSubtotal());
            $order->setAffiliateKey($account->getCode());
            $order->setAffiliateCommission(serialize($commission));
            $order->setReferralEmail($customer->getEmail());
            $order->save();
        }
    }

}