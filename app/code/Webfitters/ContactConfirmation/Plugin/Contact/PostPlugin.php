<?php
namespace Webfitters\ContactConfirmation\Plugin\Contact;

class PostPlugin {

    protected $transport;
	protected $translator;
	protected $config;
	protected $store;
	protected $escaper;

    public function __construct(
		\Magento\Framework\Mail\Template\TransportBuilder $transport,
		\Magento\Framework\Translate\Inline\StateInterface $translator,
	    \Magento\Framework\App\Config\ScopeConfigInterface $config,
	    \Magento\Store\Model\StoreManagerInterface $store,
	    \Magento\Framework\Escaper $escaper
    ) {
		$this->transport = $transport;
		$this->translator = $translator;
		$this->config = $config;
		$this->store = $store;
		$this->escaper = $escaper;
    }

    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result) {
	    $this->translator->suspend();
		$post = $subject->getRequest()->getPostValue();
	    try {
	        $postObject = new \Magento\Framework\DataObject();
	        $postObject->setData($post);
	        $transport = $this->transport
	            ->setTemplateIdentifier('contact_confirmation')
	            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
	            ->setTemplateVars(['data' => $postObject])
	            ->setFrom(['name' => '', 'email' => $this->config->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)])
	            ->addTo([$post['email']])
	            ->getTransport();
	            $transport->sendMessage();
	    } catch (\Exception $e) {}
		$this->translator->resume();
    }
}