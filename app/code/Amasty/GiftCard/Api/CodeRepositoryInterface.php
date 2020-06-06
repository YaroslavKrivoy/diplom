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
interface CodeRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\CodeInterface $code
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\CodeInterface $code);

    /**
     * Get by id
     *
     * @param int $codeId
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function getById($codeId);

    /**
     * Get by id
     *
     * @param string $code
     * @return int
     */
    public function getIdByCode($code);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\CodeInterface $code
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\CodeInterface $code);

    /**
     * Delete by id
     *
     * @param int $codeId
     * @return bool true on success
     */
    public function deleteById($codeId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * Get by code
     *
     * @param string $code
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function getByCode($code);
}
