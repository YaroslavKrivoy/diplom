<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface CustomerCardInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CUSTOMER_CARD_ID = 'customer_card_id';
    const ACCOUNT_ID = 'account_id';
    const CUSTOMER_ID = 'customer_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getCustomerCardId();

    /**
     * @param int $customerCardId
     *
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface
     */
    public function setCustomerCardId($customerCardId);

    /**
     * @return int|null
     */
    public function getAccountId();

    /**
     * @param int|null $accountId
     *
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface
     */
    public function setAccountId($accountId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface
     */
    public function setCustomerId($customerId);
}
