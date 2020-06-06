<?php
namespace Mageplaza\Affiliate\Block\Adminhtml\Order\View;

class Affiliate extends \Magento\Backend\Block\Template {

	protected $account;
	protected $request;
	protected $order;
	protected $customer;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Sales\Model\OrderFactory $order,
		\Mageplaza\Affiliate\Model\AccountFactory $account,
		\Magento\Customer\Model\CustomerFactory $customer,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->account = $account;
		$this->request = $request;
		$this->order = $order;
		$this->customer = $customer;
	}

	public function getAffiliateName(){
		$order = $this->order->create()->load($this->request->getParam('order_id'));
		$commission = @unserialize($order->getAffiliateCommission());
		if(!$order->getAffiliateKey() || !$commission || empty($commission)){
			return null;
		}
		$account = $this->account->create()->getCollection()->addFieldToFilter('code', $order->getAffiliateKey())->getFirstItem();
		$customer = $this->customer->create()->load($account->getCustomerId());
		if($customer->getCompany()){
			return $customer->getCompany();
		}
		return $customer->getFirstname().' '.$customer->getLastname();

	}

	public function getCommission(){
		$order = $this->order->create()->load($this->request->getParam('order_id'));
		$commission = @unserialize($order->getAffiliateCommission());
		if(!$order->getAffiliateKey() || !$commission || empty($commission)){
			return null;
		}
		$total = 0;
		foreach($commission as $key => $amount){
			$total += $amount;
		}
		return $total;
	}

}