<?php
namespace Webfitters\OrderCredit\Block\Adminhtml\Invoice\Totals;

class OrderCredit extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals {

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
        $invoice = $parent->getInvoice();
        if($invoice->getBaseCreditAmount() > 0 && $invoice->getCreditAmount() > 0){
            $parent->addTotal(new \Magento\Framework\DataObject([
                'code' => 'order_credit',
                'strong' => false,
                'value' => $invoice->getCreditAmount(),
                'base_value' => $invoice->getBaseCreditAmount(),
                'label' => 'Order Credit'
            ]));
        }
        return $this;
    }
}
