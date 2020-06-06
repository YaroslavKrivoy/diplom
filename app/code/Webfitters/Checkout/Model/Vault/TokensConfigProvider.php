<?php
namespace Webfitters\Checkout\Model\Vault;

class TokensConfigProvider extends \Magento\Vault\Model\Ui\Adminhtml\TokensConfigProvider {

	protected $request;
	protected $order;
	private $paymentTokenRepository;
    private $filterBuilder;
    private $searchCriteriaBuilder;
    private $session;
    private $storeManager;
    private $tokenUiComponentProviders;
    private $dateTimeFactory;
    private $paymentDataHelper;
    private $orderRepository;
    private $paymentTokenManagement;
    protected $invoice;

	public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $paymentTokenRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Intl\DateTimeFactory $dateTimeFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Sales\Model\Order\InvoiceFactory $invoice,
        array $tokenUiComponentProviders = []
    ) {
		parent::__construct($session, $paymentTokenRepository, $filterBuilder, $searchCriteriaBuilder, $storeManager, $dateTimeFactory, $tokenUiComponentProviders);
		$this->paymentTokenRepository = $paymentTokenRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->tokenUiComponentProviders = $tokenUiComponentProviders;
        $this->dateTimeFactory = $dateTimeFactory;
		$this->request = $request;
		$this->order = $order;
        $this->invoice = $invoice;
	}

	public function getTokensComponents($vaultPaymentCode) {
        $result = [];
        if($this->request->getParam('order_id')){
        	$order = $this->order->create()->load($this->request->getParam('order_id'));
        	$customerId = $order->getCustomerId();
        } else if($this->request->getParam('invoice_id')) {
        	$invoice = $this->invoice->create()->load($this->request->getParam('invoice_id'));
            $order = $this->order->create()->load($invoice->getOrderId());
            $customerId = $order->getCustomerId();
        } else {
            $customerId = $this->session->getCustomerId();
        }

        $vaultPayment = $this->getVaultPayment($vaultPaymentCode);
        
        if ($vaultPayment === null) {
            return $result;
        }
        $vaultProviderCode = $vaultPayment->getProviderCode();
        $componentProvider = $this->getComponentProvider($vaultProviderCode);
        if ($componentProvider === null) {
            return $result;
        }
        if ($customerId && $customerId > 0) {
            $this->searchCriteriaBuilder->addFilters([$this->filterBuilder->setField(\Magento\Vault\Api\Data\PaymentTokenInterface::CUSTOMER_ID)->setValue($customerId)->create()]);
        } else {
            try {
                $this->searchCriteriaBuilder->addFilters([$this->filterBuilder->setField(\Magento\Vault\Api\Data\PaymentTokenInterface::ENTITY_ID)->setValue($this->getPaymentTokenEntityId())->create()]);
            } catch (\Magento\Framework\Exception\InputException $e) {
                return $result;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return $result;
            }
        }
        $this->searchCriteriaBuilder->addFilters([$this->filterBuilder->setField(\Magento\Vault\Api\Data\PaymentTokenInterface::PAYMENT_METHOD_CODE)->setValue($vaultProviderCode)->create()]);
        $this->searchCriteriaBuilder->addFilters([$this->filterBuilder->setField(\Magento\Vault\Api\Data\PaymentTokenInterface::IS_ACTIVE)->setValue(1)->create()]);
        $this->searchCriteriaBuilder->addFilters([$this->filterBuilder->setField(\Magento\Vault\Api\Data\PaymentTokenInterface::EXPIRES_AT)->setConditionType('gt')->setValue($this->dateTimeFactory->create('now', new \DateTimeZone('UTC'))->format('Y-m-d 00:00:00'))->create()]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        foreach ($this->paymentTokenRepository->getList($searchCriteria)->getItems() as $token) {
            $result[] = $componentProvider->getComponentForToken($token);
        }
        return $result;
    }

    private function getComponentProvider($vaultProviderCode)
    {
        $componentProvider = isset($this->tokenUiComponentProviders[$vaultProviderCode])?$this->tokenUiComponentProviders[$vaultProviderCode]:null;
        return ($componentProvider instanceof \Magento\Vault\Model\Ui\TokenUiComponentProviderInterface)?$componentProvider:null;
    }

    private function getVaultPayment($vaultPaymentCode) {
        $storeId = $this->storeManager->getStore()->getId();
        $vaultPayment = $this->getPaymentDataHelper()->getMethodInstance($vaultPaymentCode);
        return $vaultPayment->isActive($storeId) ? $vaultPayment : null;
    }

    private function getPaymentTokenEntityId() {
        $paymentToken = $this->getPaymentTokenManagement()->getByPaymentId($this->getOrderPaymentEntityId());
        if ($paymentToken === null) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('No available payment tokens for specified order payment.'));
        }
        return $paymentToken->getEntityId();
    }

    private function getOrderPaymentEntityId() {
        $orderId = $this->session->getReordered()?:$this->session->getOrder()->getEntityId();
        $order = $this->getOrderRepository()->get($orderId);
        return (int) $order->getPayment()->getEntityId();
    }

    private function getPaymentDataHelper() {
        if ($this->paymentDataHelper === null) {
            $this->paymentDataHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Payment\Helper\Data::class);
        }
        return $this->paymentDataHelper;
    }

    private function getOrderRepository() {
        if ($this->orderRepository === null) {
            $this->orderRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        }
        return $this->orderRepository;
    }

    private function getPaymentTokenManagement() {
        if ($this->paymentTokenManagement === null) {
            $this->paymentTokenManagement = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Vault\Api\PaymentTokenManagementInterface::class);
        }
        return $this->paymentTokenManagement;
    }

}