<?php
/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\OrderEditor\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;
use MageWorx\OrderEditor\Gateway\Response\FraudHandler;

class Info extends ConfigurableInfo
{
    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     */
    protected function getValueView($field, $value)
    {
        switch ($field) {
            case FraudHandler::FRAUD_MSG_LIST:
                return implode('; ', $value);
        }

        return parent::getValueView($field, $value);
    }
}
