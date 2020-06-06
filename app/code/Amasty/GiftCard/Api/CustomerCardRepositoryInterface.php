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
interface CustomerCardRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\CustomerCardInterface $customerCard
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\CustomerCardInterface $customerCard);

    /**
     * Get by id
     *
     * @param int $customerCardId
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface
     */
    public function getById($customerCardId);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\CustomerCardInterface $customerCard
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\CustomerCardInterface $customerCard);

    /**
     * Delete by id
     *
     * @param int $customerCardId
     * @return bool true on success
     */
    public function deleteById($customerCardId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\CustomerCardInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
