<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api;

use Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface;

interface OrderItemBoxRepositoryInterface
{
    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface $orderItemBoxDataInterface
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderItemBoxInterface $orderItemBoxDataInterface);

    /**
     * @param int $itemId
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface $orderItemBoxDataInterface
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OrderItemBoxInterface $orderItemBoxDataInterface);

    /**
     * @param int $itemId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($itemId);
}
