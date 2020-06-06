<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface AmGiftCardOptionsInterface
{
    /**#@+
     * Constant used as key into $_data
     */
    const CARD_AMOUNT = 'am_giftcard_amount';
    const CARD_VALUE = 'am_giftcard_amount_custom';
    const SENDER_NAME = 'am_giftcard_sender_name';
    const SENDER_EMAIL = 'am_giftcard_sender_email';
    const RECIPIENT_NAME = 'am_giftcard_recipient_name';
    const RECIPIENT_EMAIL = 'am_giftcard_recipient_email';
    const DELIVERY_DATE = 'am_giftcard_date_delivery';
    const DELIVERY_TIMEZONE = 'am_giftcard_date_delivery_timezone';
    const MESSAGE = 'am_giftcard_message';
    const LIFETIME = 'am_giftcard_lifetime';
    const EMAIL_TEMPLATE = 'am_giftcard_email_template';
    const CODE_SET = 'am_giftcard_code_set';
    const IMAGE = 'am_giftcard_image';
    /**#@-*/

    /**
     * @return float|null
     */
    public function getCardAmount();

    /**
     * @param $cardAmount
     * @return $this
     */
    public function setCardAmount($cardAmount);

    /**
     * @return float|null
     */
    public function getCardValue();

    /**
     * @param $cardValue
     * @return $this
     */
    public function setCardValue($cardValue);

    /**
     * @return string|null
     */
    public function getSenderName();

    /**
     * @param $senderName
     * @return $this
     */
    public function setSenderName($senderName);

    /**
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * @param $senderEmail
     * @return $this
     */
    public function setSenderEmail($senderEmail);

    /**
     * @return string|null
     */
    public function getRecipientName();

    /**
     * @param $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName);

    /**
     * @return string|null
     */
    public function getRecipientEmail();

    /**
     * @param $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * @return string|null
     */
    public function getDeliveryDate();

    /**
     * @param $deliveryDate
     * @return $this
     */
    public function setDeliveryDate($deliveryDate);

    /**
     * @return string|null
     */
    public function getDeliveryTimezone();

    /**
     * @param $deliveryTimezone
     * @return $this
     */
    public function setDeliveryTimezone($deliveryTimezone);

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return string|null
     */
    public function getImage();

    /**
     * @param $image
     * @return $this
     */
    public function setImage($image);
}
