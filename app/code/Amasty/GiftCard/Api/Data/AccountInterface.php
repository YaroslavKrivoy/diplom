<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface AccountInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ACCOUNT_ID = 'account_id';
    const CODE_ID = 'code_id';
    const IMAGE_ID = 'image_id';
    const ORDER_ID = 'order_id';
    const WEBSITE_ID = 'website_id';
    const PRODUCT_ID = 'product_id';
    const STATUS_ID = 'status_id';
    const INITIAL_VALUE = 'initial_value';
    const CURRENT_VALUE = 'current_value';
    const EXPIRED_DATE = 'expired_date';
    const COMMENT = 'comment';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    const SENDER_MESSAGE = 'sender_message';
    const IMAGE_PATH = 'image_path';
    const DATE_DELIVERY = 'date_delivery';
    const IS_SENT = 'is_sent';
    const CUSTOMER_CREATED_ID = 'customer_created_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param int $accountId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setAccountId($accountId);

    /**
     * @return int
     */
    public function getCodeId();

    /**
     * @param int $codeId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setCodeId($codeId);

    /**
     * @return int|null
     */
    public function getImageId();

    /**
     * @param int|null $imageId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setImageId($imageId);

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @param int|null $orderId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setOrderId($orderId);

    /**
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * @param int|null $websiteId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int|null
     */
    public function getProductId();

    /**
     * @param int|null $productId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setProductId($productId);

    /**
     * @return int
     */
    public function getStatusId();

    /**
     * @param int $statusId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setStatusId($statusId);

    /**
     * @return float
     */
    public function getInitialValue();

    /**
     * @param float $initialValue
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setInitialValue($initialValue);

    /**
     * @return float
     */
    public function getCurrentValue();

    /**
     * @param float $currentValue
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setCurrentValue($currentValue);

    /**
     * @return string|null
     */
    public function getExpiredDate();

    /**
     * @param string|null $expiredDate
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setExpiredDate($expiredDate);

    /**
     * @return string|null
     */
    public function getComment();

    /**
     * @param string|null $comment
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setComment($comment);

    /**
     * @return string|null
     */
    public function getSenderName();

    /**
     * @param string|null $senderName
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setSenderName($senderName);

    /**
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * @param string|null $senderEmail
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setSenderEmail($senderEmail);

    /**
     * @return string|null
     */
    public function getRecipientName();

    /**
     * @param string|null $recipientName
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setRecipientName($recipientName);

    /**
     * @return string|null
     */
    public function getRecipientEmail();

    /**
     * @param string|null $recipientEmail
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * @return string|null
     */
    public function getSenderMessage();

    /**
     * @param string|null $senderMessage
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setSenderMessage($senderMessage);

    /**
     * @return string|null
     */
    public function getImagePath();

    /**
     * @param string|null $imagePath
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setImagePath($imagePath);

    /**
     * @return string|null
     */
    public function getDateDelivery();

    /**
     * @param string|null $dateDelivery
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setDateDelivery($dateDelivery);

    /**
     * @return int
     */
    public function getIsSent();

    /**
     * @param int $isSent
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setIsSent($isSent);

    /**
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function getCustomerCreatedId();

    /**
     * @param int $customerCreatedId
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function setCustomerCreatedId($customerCreatedId);
}
