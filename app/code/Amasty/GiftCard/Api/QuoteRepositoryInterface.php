<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api;

/**
 * @api
 */
interface QuoteRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\QuoteInterface $quote
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\QuoteInterface $quote);

    /**
     * Get by id
     *
     * @param int $entityId
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\QuoteInterface $quote
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\QuoteInterface $quote);

    /**
     * Delete by id
     *
     * @param int $entityId
     * @return bool true on success
     */
    public function deleteById($entityId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * Get by quote id
     *
     * @param int $quoteId
     * @return \Amasty\GiftCard\Api\Data\QuoteInterface
     */
    public function getByQuoteId($quoteId);
}
