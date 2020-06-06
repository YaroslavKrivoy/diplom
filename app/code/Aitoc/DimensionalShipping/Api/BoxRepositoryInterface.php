<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api;

use Aitoc\DimensionalShipping\Api\Data\BoxInterface;

interface BoxRepositoryInterface
{
    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\BoxInterface $boxModel
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\BoxInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(BoxInterface $boxModel);

    /**
     * @param int $itemId
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\BoxInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($itemId);

    /**
     * @param \Aitoc\DimensionalShipping\Api\Data\BoxInterface $boxModel
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(BoxInterface $boxModel);

    /**
     * @param int $itemId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($itemId);
}
