<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Form\Element;


class CustomDate extends \Magento\Framework\Data\Form\Element\Date
{
    /**
     * Set date value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        if (empty($value)) {
            $this->_value = '';
            return $this;
        }

        if ($value instanceof \DateTimeInterface) {
            $this->_value = $value;
            return $this;
        }

        if (preg_match('/^[0-9]+$/', $value)) {
            $this->_value = (new \DateTime())->setTimestamp($this->_toTimestamp($value));
            return $this;
        }

        try {
            $this->_value = new \DateTime($value, new \DateTimeZone(date_default_timezone_get()));
            $this->_value->setTimezone(new \DateTimeZone($this->localeDate->getConfigTimezone()));
        } catch (\Exception $e) {
            $this->_value = '';
        }

        return $this;
    }
}