<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface QuoteInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const CODE_ID = 'code_id';
    const ACCOUNT_ID = 'account_id';
    const GIFT_AMOUNT = 'gift_amount';
    const BASE_GIFT_AMOUNT = 'base_gift_amount';
    const CODE = 'code';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $quoteId
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @return int
     */
    public function getCodeId();

    /**
     * @param int $codeId
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setCodeId($codeId);

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param int $accountId
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setAccountId($accountId);

    /**
     * @return float
     */
    public function getGiftAmount();

    /**
     * @param float $giftAmount
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setGiftAmount($giftAmount);

    /**
     * @return float
     */
    public function getBaseGiftAmount();

    /**
     * @param float $baseGiftAmount
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setBaseGiftAmount($baseGiftAmount);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function setCode($code);
}
