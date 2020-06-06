<?php
namespace Webfitters\HearAbout\Block\Adminhtml\Order\Create;

class HearAbout extends \Magento\Backend\Block\Template {

	protected $hearabout;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->hearabout = $hearabout;
	}

	public function getSources(){
		return $this->hearabout->create()->getCollection()->setOrder('name', 'ASC');
	}
	
}