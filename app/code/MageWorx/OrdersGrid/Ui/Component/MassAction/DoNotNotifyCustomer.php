<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Ui\Component\MassAction;

use Zend\Stdlib\JsonSerializable;

class DoNotNotifyCustomer extends OptionsAbstract implements JsonSerializable
{
    const SEND_EMAIL = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if (empty($this->options)) {
            $this->prepareOptionsData();

            /**
             * Capture
             * Invoice
             * Invoice → Print
             * Ship
             * Ship → Print
             * Invoice → Capture
             * Invoice → Capture → Ship
             * Invoice → Capture → Ship → Print
             */
            $this->getMatchingOptions();
            $this->options = array_values($this->options);
        }

        return $this->options;
    }
}
