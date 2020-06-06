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
interface ImageRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\GiftCard\Api\Data\ImageInterface $image
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function save(\Amasty\GiftCard\Api\Data\ImageInterface $image);

    /**
     * Get by id
     *
     * @param int $imageId
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function getById($imageId);

    /**
     * Delete
     *
     * @param \Amasty\GiftCard\Api\Data\ImageInterface $image
     * @return bool true on success
     */
    public function delete(\Amasty\GiftCard\Api\Data\ImageInterface $image);

    /**
     * Delete by id
     *
     * @param int $imageId
     * @return bool true on success
     */
    public function deleteById($imageId);

    /**
     * Lists
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
