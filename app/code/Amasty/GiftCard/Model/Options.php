<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterface as AmGiftCardOptionsInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class Options extends AbstractSimpleObject implements AmGiftCardOptionsInterface, ArrayInterface
{
    const GIFT_PARAMS = [
        'am_giftcard_amount' => 'Card Value',
        'am_giftcard_amount_custom' => 'Card Value',
        'am_giftcard_sender_name' => 'Sender Name',
        'am_giftcard_sender_email' => 'Sender Email',
        'am_giftcard_recipient_name' => 'Recipient Name',
        'am_giftcard_recipient_email' => 'Recipient Email',
        'am_giftcard_date_delivery' => 'Delivery Date',
        'am_giftcard_date_delivery_timezone' => 'Delivery Timezone',
        'am_giftcard_message' => 'Message'
    ];

    const GIFT_SENDER_NAME = 'am_giftcard_sender_name';
    const GIFT_SENDER_EMAIL = 'am_giftcard_sender_email';
    const GIFT_RECIPIENT_NAME= 'am_giftcard_recipient_name';
    const GIFT_DATE_DELIVERY= 'am_giftcard_date_delivery';
    const GIFT_MESSAGE = 'allow_message';

    /**
     * @param $giftOptions
     * @return array
     */
    public function getGiftOptions($giftOptions)
    {
        $options = [];
        foreach ($giftOptions as $giftOption => $giftValue) {
            if (array_key_exists($giftOption, self::GIFT_PARAMS) && $giftValue) {
                $options[] = [
                    'label' => __(self::GIFT_PARAMS[$giftOption]),
                    'value' => $giftValue,
                    'print_value' => $giftValue,
                    'option_type' => 'field',
                    'option_view' => false,
                ];
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::GIFT_SENDER_NAME,
                'label' => __('Sender name')
            ],
            [
                'value' => self::GIFT_SENDER_EMAIL,
                'label' => __('Sender Email')
            ],
            [
                'value' => self::GIFT_RECIPIENT_NAME,
                'label' => __('Recipient name')
            ],
            [
                'value' => self::GIFT_DATE_DELIVERY,
                'label' => __('Delivery date')
            ],
            [
                'value' => self::GIFT_MESSAGE,
                'label' => __('Gift Card message')
            ]
        ];
    }

    /**
     * @return float|null
     */
    public function getCardAmount()
    {
        return $this->_get(self::CARD_AMOUNT);
    }

    /**
     * @param $cardAmount
     * @return $this
     */
    public function setCardAmount($cardAmount)
    {
        $this->setData(self::CARD_AMOUNT, $cardAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCardValue()
    {
        return $this->_get(self::CARD_VALUE);
    }

    /**
     * @param $cardValue
     * @return $this
     */
    public function setCardValue($cardValue)
    {
        $this->setData(self::CARD_VALUE, $cardValue);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderName()
    {
        return $this->_get(self::SENDER_NAME);
    }

    /**
     * @param $senderName
     * @return $this
     */
    public function setSenderName($senderName)
    {
        $this->setData(self::SENDER_NAME, $senderName);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderEmail()
    {
        return $this->_get(self::SENDER_EMAIL);
    }

    /**
     * @param $senderEmail
     * @return $this
     */
    public function setSenderEmail($senderEmail)
    {
        $this->setData(self::SENDER_EMAIL, $senderEmail);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipientName()
    {
        return $this->_get(self::RECIPIENT_NAME);
    }

    /**
     * @param $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName)
    {
        $this->setData(self::RECIPIENT_NAME, $recipientName);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRecipientEmail()
    {
        return $this->_get(self::RECIPIENT_EMAIL);
    }

    /**
     * @param $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeliveryDate()
    {
        return $this->_get(self::DELIVERY_DATE);
    }

    /**
     * @param $deliveryDate
     * @return $this
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->setData(self::DELIVERY_DATE, $deliveryDate);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeliveryTimezone()
    {
        return $this->_get(self::DELIVERY_TIMEZONE);
    }

    /**
     * @param $deliveryTimezone
     * @return $this
     */
    public function setDeliveryTimezone($deliveryTimezone)
    {
        $this->setData(self::DELIVERY_TIMEZONE, $deliveryTimezone);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
        return $this;
    }
}
