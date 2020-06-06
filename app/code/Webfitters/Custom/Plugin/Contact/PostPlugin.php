<?php
namespace Webfitters\Custom\Plugin\Contact;

class PostPlugin {

    private $_dataPersistor;
    private $_redirect;
	private $url;

    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
		\Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
		$this->url = $url;
        $this->_redirect = $redirect;
    }

    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result) {
        $this->_redirect->redirect($subject->getResponse(), $this->url->getUrl('contact'));
    }
}