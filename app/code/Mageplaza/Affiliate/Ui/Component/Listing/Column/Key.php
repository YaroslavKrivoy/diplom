<?php
namespace Mageplaza\Affiliate\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class Key extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;
    protected $account;
    protected $customer;

    public function __construct(
        \Mageplaza\Affiliate\Model\AccountFactory $account,
        \Magento\Customer\Model\CustomerFactory $customer, 
        ContextInterface $context, 
        UiComponentFactory $uiComponentFactory, 
        OrderRepositoryInterface $orderRepository, 
        SearchCriteriaBuilder $criteria, 
        array $components = [], 
        array $data = []
    ) {
        $this->customer = $customer;
        $this->account = $account;
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {


        /*$customerId = '';
        if (is_object($account)) {
            $customerId = $account->getCustomerId();
        } else {
            $account = $this->accountFactory->create()->load($account);
            if ($account->getId())
                $customerId = $account->getCustomerId();
        }

        $customer = $this->customerFactory->create()->load($customerId);
        if ($customer->getId()) {
            return $customer->getEmail();
        }

        return '';*/


       

        /*if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $value = '';
                $order  = $this->_orderRepository->get($item["entity_id"]);
                if($order->getData("affiliate_key")){
                    $account = $this->account->create()->loadByCode($order->getData("affiliate_key"));
                    if($account->getId()){
                        $customer = $this->customer->create()->load($account->getCustomerId());
                        if($customer->getId()){
                            $value = $customer->getEmail();
                        }
                    }
                }
                $item[$this->getData('name')] = $value;
            }
        }*/

        return $dataSource;
    }
}