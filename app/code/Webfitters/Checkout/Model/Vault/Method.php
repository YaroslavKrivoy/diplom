<?php
namespace Webfitters\Checkout\Model\Vault;

class Method extends \Magento\Vault\Model\Method\Vault {

    const TOKEN_METADATA_KEY = 'token_metadata';
    private static $activeKey = 'active';
    private static $titleKey = 'title';
    private $configFactory;
    private $config;
    private $vaultProvider;
    private $objectManager;
    private $storeId;
    private $valueHandlerPool;
    private $eventManager;
    private $commandManagerPool;
    private $tokenManagement;
    private $paymentExtensionFactory;
    private $code;

    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Magento\Payment\Gateway\ConfigFactoryInterface $configFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Payment\Model\MethodInterface $vaultProvider,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Payment\Gateway\Config\ValueHandlerPoolInterface $valueHandlerPool,
        \Magento\Payment\Gateway\Command\CommandManagerPoolInterface $commandManagerPool,
        \Magento\Vault\Api\PaymentTokenManagementInterface $tokenManagement,
        \Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\App\Request\Http $request,
        $code
    ) {
        parent::__construct($config, $configFactory, $objectManager, $vaultProvider, $eventManager, $valueHandlerPool, $commandManagerPool, $tokenManagement, $paymentExtensionFactory, $code);
        $this->config = $config;
        $this->configFactory = $configFactory;
        $this->objectManager = $objectManager;
        $this->valueHandlerPool = $valueHandlerPool;
        $this->vaultProvider = $vaultProvider;
        $this->eventManager = $eventManager;
        $this->commandManagerPool = $commandManagerPool;
        $this->tokenManagement = $tokenManagement;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->code = $code;
        $this->request = $request;
        $this->order = $order;
    }

    private function attachTokenExtensionAttribute(OrderPaymentInterface $orderPayment) {
        $additionalInformation = $orderPayment->getAdditionalInformation();
        if (empty($additionalInformation[\Magento\Vault\Api\Data\PaymentTokenInterface::PUBLIC_HASH])) {
            throw new \LogicException('Public hash should be defined');
        }
        if($this->request->getParam('order_id')){
            $order = $this->order->create()->load($this->request->getParam('order_id'));
            $customerId = $order->getCustomerId();
        } else {
            $customerId = isset($additionalInformation[\Magento\Vault\Api\Data\PaymentTokenInterface::CUSTOMER_ID])?$additionalInformation[\Magento\Vault\Api\Data\PaymentTokenInterface::CUSTOMER_ID]:null;
        }
        $publicHash = $additionalInformation[\Magento\Vault\Api\Data\PaymentTokenInterface::PUBLIC_HASH];
        $paymentToken = $this->tokenManagement->getByPublicHash($publicHash, $customerId);
        if ($paymentToken === null) {
            throw new \LogicException("No token found");
        }
        $extensionAttributes = $this->getPaymentExtensionAttributes($orderPayment);
        $extensionAttributes->setVaultPaymentToken($paymentToken);
    }

}