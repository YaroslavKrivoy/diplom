<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Model\Order\Total\Invoice;

use Amasty\GiftCard\Model\Order\Total\GiftCardTotal;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magento\Sales\Model\Order\Invoice;

class GiftCard extends AbstractTotal
{
    use GiftCardTotal;

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $this->collectGiftTotals($invoice);

        return $this;
    }
}
