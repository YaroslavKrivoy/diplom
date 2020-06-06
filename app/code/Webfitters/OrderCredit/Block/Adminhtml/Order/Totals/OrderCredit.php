<?php
namespace Webfitters\OrderCredit\Block\Adminhtml\Order\Totals;

class OrderCredit extends \Magento\Sales\Block\Adminhtml\Order\Totals {

    protected $_adminHelper;
    protected $commissionHelper;
    protected $customer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    public function initTotals() {
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        if($order->getBaseCreditAmount() > 0 && $order->getCreditAmount() > 0){
            $parent->addTotal(new \Magento\Framework\DataObject([
                'code' => 'order_credit',
                'strong' => false,
                'value' => $order->getCreditAmount(),
                'base_value' => $order->getBaseCreditAmount(),
                'label' => 'Order Credit'
            ]));
        }
        return $this;
    }
}
