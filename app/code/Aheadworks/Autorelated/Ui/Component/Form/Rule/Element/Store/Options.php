<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\Form\Rule\Element\Store;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class Options
 *
 * @package Aheadworks\Autorelated\Ui\Component\Form\Rule\Element\Store
 */
class Options extends StoreOptions
{
    /**
     * @var array
     */
    private $storeListOptions;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->generateCurrentOptions();
        $this->options = array_values($this->currentOptions);

        return $this->options;
    }

    /**
     * Get store list
     *
     * @return array
     */
    public function getStoreList()
    {
        if ($this->storeListOptions !== null) {
            return $this->storeListOptions;
        }

        /** @var StoreInterface $store */
        foreach ($this->systemStore->getStoreCollection() as $store) {
            $this->storeListOptions[] = [
                'label' => $store->getName(),
                'value' => $store->getId()
            ];
        }

        $this->storeListOptions = array_values($this->storeListOptions);

        return $this->storeListOptions;
    }
}
