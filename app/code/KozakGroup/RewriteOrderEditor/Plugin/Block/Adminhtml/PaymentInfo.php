<?php

namespace KozakGroup\RewriteOrderEditor\Plugin\Block\Adminhtml;

use Magento\Payment\Block\Info;

class PaymentInfo
{
    public function aroundToPdf(
        Info $subject,
        callable $proceed
    ) {
        $subject->setTemplate('KozakGroup_RewriteOrderEditor::info/pdf/default.phtml');
        return $subject->toHtml();
    }
}