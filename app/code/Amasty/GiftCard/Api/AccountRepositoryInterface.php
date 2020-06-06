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
interface AccountRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\AccountInterface $account
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\AccountInterface $account);

    /**
     * Save current account
     *
     * @param \Amasty\GiftCard\Api\Data\AccountInterface $account
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function saveCurrent(\Amasty\GiftCard\Api\Data\AccountInterface $account);

    /**
     * Get by id
     *
     * @param int $accountId
     * @return \Amasty\GiftCard\Api\Data\AccountInterface
     */
    public function getById($accountId);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\AccountInterface $account
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\AccountInterface $account);

    /**
     * Delete by id
     *
     * @param int $accountId
     * @return bool true on success
     */
    public function deleteById($accountId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\AccountInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
