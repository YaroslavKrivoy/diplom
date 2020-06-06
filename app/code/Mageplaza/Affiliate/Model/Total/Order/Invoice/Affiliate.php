<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Model\Total\Order\Invoice;

class Affiliate extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * Collect invoice subtotal
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $rate = ($order->getBaseGrandTotal() > 0)?$invoice->getBaseGrandTotal() / $order->getBaseGrandTotal():0;

        $commission = $order->getAffiliateCommission();
        if($commission){
            $commission = @unserialize($commission);
            $invoiceCommission = [];
            foreach($commission as $id => $com){
                $invoiceCommission[$id] = $invoice->roundPrice($com * $rate, 'commission', true);
            }

            $invoice->setAffiliateCommission($invoiceCommission);
        }

        $baseOrderDiscount = $order->getBaseAffiliateDiscountAmount();
        if($baseOrderDiscount) {
            $orderDiscount = $order->getAffiliateDiscountAmount();

            $affiliateDiscount     = $invoice->roundPrice($orderDiscount * $rate, 'regular', true);
            $baseAffiliateDiscount = $invoice->roundPrice($baseOrderDiscount * $rate, 'base', true);

            foreach ($invoice->getOrder()->getInvoiceCollection() as $previousInvoice) {
                $baseOrderDiscount -= $previousInvoice->getBaseAffiliateDiscountAmount();
                $orderDiscount -= $previousInvoice->getAffiliateDiscountAmount();
            }

            if ($invoice->isLast()) {
                $affiliateDiscount     = $orderDiscount;
                $baseAffiliateDiscount = $baseOrderDiscount;
            } else {
                $affiliateDiscount     = max($orderDiscount, $affiliateDiscount);
                $baseAffiliateDiscount = max($baseOrderDiscount, $baseAffiliateDiscount);
            }

            $invoice->setAffiliateDiscountAmount($affiliateDiscount);
            $invoice->setBaseAffiliateDiscountAmount($baseAffiliateDiscount);

            $invoice->setGrandTotal($invoice->getGrandTotal() + $affiliateDiscount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseAffiliateDiscount);
        }

        return $this;
    }
}
