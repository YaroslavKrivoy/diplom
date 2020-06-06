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
interface CodeSetRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\CodeSetInterface $codeSet
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\CodeSetInterface $codeSet);

    /**
     * Get by id
     *
     * @param int $codeSetId
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function getById($codeSetId);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\CodeSetInterface $codeSet
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\CodeSetInterface $codeSet);

    /**
     * Delete by id
     *
     * @param int $codeSetId
     * @return bool true on success
     */
    public function deleteById($codeSetId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
