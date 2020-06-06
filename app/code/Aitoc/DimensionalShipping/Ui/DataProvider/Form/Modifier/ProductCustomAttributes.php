<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\DimensionalShipping\Ui\DataProvider\Form\Modifier;

use Aitoc\DimensionalShipping\Helper\Data;
use Aitoc\DimensionalShipping\Model\ProductOptionsRepository;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class AitocFields
 */
class ProductCustomAttributes extends AbstractModifier
{
    private $fields;
    private $optionsRepository;
    private $locator;
    private $containerData;
    private $helper;

    public function __construct(
        Data $helper,
        ProductOptionsRepository $optionsRepository,
        LocatorInterface $locator
    ) {
        $this->helper            = $helper;
        $this->fields            = $helper->getFields();
        $this->containerData     = $helper->getContainerData();
        $this->optionsRepository = $optionsRepository;
        $this->locator           = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId   = $this->locator->getProduct()->getEntityId();
        $productData = $this->optionsRepository->getByProductId($productId)->getData();
        if (!$productData) {
            return $data;
        }
        foreach ($this->getFields() as $field) {
            $data[$productId][static::DATA_SOURCE_DEFAULT][$field] = $productData[$field];
        }

        return $data;
    }

    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $productId   = $this->locator->getProduct()
            ->getEntityId();
        $productType = $this->locator->getProduct()->getTypeId();
        if ($productType == 'grouped' || $productType == 'downloadable' || $productType == 'virtual') {
            return $meta;
        }
        $productData                                                         = $this->optionsRepository->getByProductId(
            $productId
        )->getData();
        $meta['dimensional_shipping_options']['arguments']['data']['config'] = [
            'componentType' => 'fieldset',
            'label'         => __('Dimensional Shipping Options'),
            'collapsible'   => true,
            'dataScope'     => 'data.product',
            'sortOrder'     => 100
        ];
        foreach ($this->getFields() as $attributeCode) {
            if (array_key_exists('container_' . $attributeCode, $this->getContainerData())) {
                $meta['dimensional_shipping_options']['children']['container_'
                . $attributeCode] = $this->getContainerData($attributeCode);
            }
        }
        if (isset($productData['special_box'])) {
            if ($productData['special_box'] == 1) {
                $meta['dimensional_shipping_options']['children']['container_select_box']['children']['select_box']
                ['arguments']['data']['config']['visible'] = 1;
            }
        }
        $meta['dimensional_shipping_options']['children']['container_select_box']['children']['select_box']['arguments']
        ['data']['config']['options'] = $this->helper->getBoxList();

        return $meta;
    }

    public function getContainerData($attributeCode = null)
    {
        if ($attributeCode) {
            return $this->containerData['container_' . $attributeCode];
        } else {
            return $this->containerData;
        }
    }
}
