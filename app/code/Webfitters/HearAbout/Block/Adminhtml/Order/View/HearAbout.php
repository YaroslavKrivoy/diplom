<?php
namespace Webfitters\HearAbout\Block\Adminhtml\Order\View;

class HearAbout extends \Magento\Backend\Block\Template {

	protected $hearabout;
	protected $request;
	protected $order;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Sales\Model\OrderFactory $order,
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->hearabout = $hearabout;
		$this->request = $request;
		$this->order = $order;
	}

	public function getHearAbout(){
		$order = $this->order->create()->load($this->request->getParam('order_id'));
		if(!$order->getHearAboutId()){
			return 'N/A';
		}
		return $this->hearabout->create()->load($order->getHearAboutId())->getName();

	}

}