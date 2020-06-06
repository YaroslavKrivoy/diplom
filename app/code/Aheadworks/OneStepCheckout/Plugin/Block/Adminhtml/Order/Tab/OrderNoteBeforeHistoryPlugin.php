<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Plugin\Block\Adminhtml\Order\Tab;

use Magento\Sales\Block\Adminhtml\Order\View\Tab\History;
use Magento\Backend\Block\Template;

/**
 * Class OrderNoteBeforeHistoryPlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Block\Adminhtml\Order\Tab
 */
class OrderNoteBeforeHistoryPlugin
{
    /**
     * Path to order note template
     */
    const ORDER_NOTE_TEMPLATE = 'Aheadworks_OneStepCheckout::order/tab/history/order_note.phtml';

    /**
     * Add order note html code before history
     *
     * @param $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        $orderNoteBlock = $subject->getLayout()->createBlock(Template::class);
        $orderNoteHtml = $orderNoteBlock
            ->setTemplate(self::ORDER_NOTE_TEMPLATE)
            ->setData(['order' => $subject->getOrder()])
            ->toHtml();

        return $orderNoteHtml . $resultHtml;
    }
}
