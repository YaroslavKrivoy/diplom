<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\Order\Email;

use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Store\Model\Store;

/**
 * Class VariableProcessor
 * @package Aheadworks\OneStepCheckout\Model\Order\Email
 */
class VariableProcessor
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @param TimezoneInterface $timezone
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        TimezoneInterface $timezone,
        ResolverInterface $localeResolver
    ) {

        $this->timezone = $timezone;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Add delivery date variables to order
     *
     * @param Order $order
     */
    public function addDeliveryDateVariables(Order $order)
    {
        $deliveryDate = $order->getData('aw_delivery_date');
        $deliveryDateFrom = $order->getData('aw_delivery_date_from');
        $deliveryDateTo = $order->getData('aw_delivery_date_to');
        $store = $order->getStore();

        if ($deliveryDate) {
            $deliveryDate = $this->getFormattedDateTime(
                $deliveryDate,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::NONE,
                $store
            );
            $order->setData('aw_delivery_date_formatted', $deliveryDate);
        }
        if ($deliveryDateFrom && $deliveryDateTo) {
            $fromTimeFormatted = $this->getFormattedDateTime(
                $deliveryDateFrom,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $store
            );
            $toTimeFormatted = $this->getFormattedDateTime(
                $deliveryDateTo,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
                $store
            );
            $order->setData('aw_delivery_time_formatted', $fromTimeFormatted . ' - ' . $toTimeFormatted);
        }
    }

    /**
     * Get formatted Date Time
     *
     * @param string $date
     * @param string $dateType
     * @param string $dateTime
     * @param Store $store
     * @return string
     */
    private function getFormattedDateTime($date, $dateType, $dateTime, $store)
    {
        return $this->timezone->formatDateTime(
            $date,
            $dateType,
            $dateTime,
            $this->localeResolver->getDefaultLocale(),
            $this->timezone->getConfigTimezone('store', $store)
        );
    }
}
