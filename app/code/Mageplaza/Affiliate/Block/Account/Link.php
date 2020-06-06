<?php
namespace Mageplaza\Affiliate\Block\Account;

class Link extends \Magento\Framework\View\Element\Html\Link\Current {

	protected $account;
	protected $customer;
	protected $config;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Mageplaza\Affiliate\Model\Account $account,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->account = $account;
        $this->customer = $customer;
        $this->config = $config;
    }

	protected function _toHtml() {
		$account = $this->account->loadByCustomerId($this->customer->getCustomer()->getId());
		$default = $this->config->getValue('affiliate/account/sign_up/default_group', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($account->getGroupId() == $default){
			return '';
		}
		return parent::_toHtml();
    }

}