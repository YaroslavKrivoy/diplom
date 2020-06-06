<?php
namespace Webfitters\Slideshow\Block;

class Slideshow extends \Magento\Framework\View\Element\Template {
	
	protected $store;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $store,
		array $data=[]
	) {
		$this->store=$store;
		parent::__construct($context, $data);
	}

	public function getSlides(){
		$data=[];
		foreach($this->getData() as $key => $val){
			if(strpos($key, 'image') !== FALSE && strpos($key, '_') === FALSE){
				$number = str_replace('image', '', $key);
				$data[intval($number)]= (object)[
					'image' => $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$val,
					'text' => $this->getData('text'.$number),
					'heading' => $this->getData('heading'.$number),
					'link' => $this->getData('link'.$number),
					'button' => $this->getData('button'.$number)
				];
			}
		}
		ksort($data, SORT_NUMERIC);
		return $data;
	}

}
?>