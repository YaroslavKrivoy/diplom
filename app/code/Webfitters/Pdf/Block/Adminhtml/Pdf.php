<?php
namespace Webfitters\Pdf\Block\Adminhtml;

class Pdf extends \Magento\Backend\Block\Template {

	protected $pdf;
	protected $store;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Webfitters\Pdf\Model\PdfFactory $pdf,
        \Magento\Store\Model\StoreManagerInterface $store,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->pdf = $pdf;
		$this->store = $store;
	}

	public function getBaseUrl(){
		return $this->store->getStore()->getBaseUrl();
	}

	public function getImageUrl($pdf){
 		return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/pdf/'.$pdf->getFile().'.png';
	}

	public function getPdfs(){
		return $this->pdf->create()->getCollection()->setOrder('created_at', 'desc');
	}

}