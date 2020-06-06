<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Block\Wvtav\Adminhtml\System\Config;

use \Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class EnableFunctionality
 * @package Aheadworks\Autorelated\Block\Wvtav\Adminhtml\System\Config
 */
class EnableFunctionality extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addCustomAfterElementHtml($element);
        $res = parent::_getElementHtml($element);
        return $res;
    }

    /**
     * Adding custom html after rendered element
     *
     * @param AbstractElement $element
     */
    private function addCustomAfterElementHtml(AbstractElement $element)
    {
        $currentAfterElementHtml = $element->getAfterElementHtml();
        $customAfterElementHtml = $this->getCustomAfterElementHtml($element);
        $element->setAfterElementHtml($currentAfterElementHtml . $customAfterElementHtml);
    }

    /**
     * Retrieve custom html for adding after rendered element
     *
     * @param AbstractElement $element
     * @return string
     */
    private function getCustomAfterElementHtml(AbstractElement $element)
    {
        $elementId = $element->getId();
        $preparedConfirmTitle = json_encode(__('Information'));
        $preparedConfirmMessage = json_encode(
            __('Are you sure you want to deactivate "Who Viewed This Also Viewed" ' .
                'functionality? This action will disable all the blocks in the WVTAV mode.')
        );
        $preparedConfirmOkButtonText = json_encode(__('Yes'));

        $customAfterElementHtml = <<<HTML
<script type="text/x-magento-init">
{
    "*": {
        "awArpSelectWithConfirmModal": {
            "selectId": "{$elementId}",
            "disablingConfirmTitle": {$preparedConfirmTitle},
            "disablingConfirmMessage": {$preparedConfirmMessage},
            "disablingConfirmOkButtonText": {$preparedConfirmOkButtonText}
        }
    }
}
</script>
HTML;

        return $customAfterElementHtml;
    }
}
