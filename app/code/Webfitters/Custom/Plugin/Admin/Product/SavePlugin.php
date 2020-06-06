<?php
namespace Webfitters\Custom\Plugin\Admin\Product;

class SavePlugin {

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

    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject, $result) {
        return $result->setPath('catalog/*/', ['store' => $subject->getRequest()->getParam('store', 0)]);
    }

}