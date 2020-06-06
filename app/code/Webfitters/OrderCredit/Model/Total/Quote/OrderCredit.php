<?php
namespace Webfitters\OrderCredit\Model\Total\Quote;

class OrderCredit extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

    protected $request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->request = $request;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shipping,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shipping, $total);
        if($shipping->getShipping()->getAddress()->getAddressType() != 'shipping'){
            return $this;
        }
        $o = $this->request->getParam('order');
        if($o && isset($o['order_credit'])){
            $credit = floatval($this->request->getParam('order')['order_credit']);
            $quote->setBaseCreditAmount($credit);
            $quote->setCreditAmount($credit);
        }
        $credit = $quote->getBaseCreditAmount();
        $total->addTotalAmount('order_credit', $credit * -1);
        $total->addBaseTotalAmount('order_credit', $credit * -1);
        return $this;
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
        if($quote->getBaseCreditAmount() > 0 && $quote->getBaseCreditAmount() > 0){
            return [
                'code' => 'order_credit',
                'title' => 'Order Credit',
                'value' => $quote->getCreditAmount()
            ];
        }
    }

}