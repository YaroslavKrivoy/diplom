<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api;

use Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface;

interface OrderBoxRepositoryInterface
{
    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface $orderDataInterface
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderBoxInterface $orderBoxDataInterface);

    /**
     * @param int $itemId
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface $orderDataInterface
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OrderBoxInterface $orderDataInterface);

    /**
     * @param int $itemId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($itemId);
}
