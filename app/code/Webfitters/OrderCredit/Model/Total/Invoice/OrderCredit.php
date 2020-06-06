<?php
namespace Webfitters\OrderCredit\Model\Total\Invoice;

class OrderCredit extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {

	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
        $invoice->setGrandTotal($invoice->getGrandTotal() - $invoice->getCreditAmount());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $invoice->getBaseCreditAmount());
        return $this;
    }

}