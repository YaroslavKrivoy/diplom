<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Order\Total\Creditmemo;

use Amasty\GiftCard\Model\Order\Total\GiftCardTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class GiftCard extends AbstractTotal
{
    use GiftCardTotal;

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $this->collectGiftTotals($creditmemo);

        return $this;
    }
}
