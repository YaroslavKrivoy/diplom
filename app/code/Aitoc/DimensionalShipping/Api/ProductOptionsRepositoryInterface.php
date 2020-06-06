<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api;

use Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface;

interface ProductOptionsRepositoryInterface
{
    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface $productOptionsDataInterface
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProductOptionsInterface $productOptionsDataInterface);

    /**
     * @param int $itemId
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface $productOptionsDataInterface
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProductOptionsInterface $productOptionsDataInterface);

    /**
     * @param int $itemId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($itemId);
}
