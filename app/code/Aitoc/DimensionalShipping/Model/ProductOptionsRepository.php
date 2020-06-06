<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */


namespace Aitoc\DimensionalShipping\Model;

use Aitoc\DimensionalShipping\Api\Data;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductOptionsRepository implements \Aitoc\DimensionalShipping\Api\ProductOptionsRepositoryInterface
{
    private $productOptionsResource;
    private $productOptionsFactory;
    private $unitsConfigFieldsLong = [];
    private $unitsConfigFieldsWeight = [];
    private $entities = [];

    /**
     * ProductOptionsRepository constructor.
     *
     * @param ResourceModel\ProductOptions $productOptionsResource
     * @param ProductOptionsFactory        $productOptionsFactory
     */
    public function __construct(
        \Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions $productOptionsResource,
        \Aitoc\DimensionalShipping\Model\ProductOptionsFactory $productOptionsFactory
    ) {
        $this->unitsConfigFieldsLong = ['width', 'height', 'length'];
        $this->unitsConfigFieldsWeight = ['weight'];
        $this->productOptionsResource = $productOptionsResource;
        $this->productOptionsFactory = $productOptionsFactory;
    }

    /**
     * @param Data\ProductOptionsInterface $productOptionsInterface
     *
     * @return Data\ProductOptionsInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\ProductOptionsInterface $productOptionsInterface)
    {
        if ($productOptionsInterface->getItemId()) {
            $productOptionsInterface = $this->get($productOptionsInterface->getItemId())
                ->addData($productOptionsInterface->getData());
        }
        try {
            $this->productOptionsResource->save($productOptionsInterface);
            unset($this->entities);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $productOptionsInterface->getItemId()));
        }

        return $productOptionsInterface;
    }

    /**
     * @param int $itemId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function get($itemId)
    {
        if (!isset($this->entities[$itemId])) {
            /** var \Aitoc\DimensionalShipping\Model\Box $productOptionsInterface */
            $productOptionsInterface = $this->productOptionsFactory->create();
            $this->productOptionsResource->load($productOptionsInterface, $itemId);
            if (!$productOptionsInterface->getItemId()) {
                throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $itemId));
            }
            $this->entities[$itemId] = $productOptionsInterface;
        }

        return $this->entities[$itemId];
    }

    /**
     * @param $productId
     *
     * @return ProductOptions
     */
    public function getByProductId($productId)
    {
        $model = $this->productOptionsFactory->create();
        $this->productOptionsResource->load($model, $productId, 'product_id');

        return $model;
    }

    /**
     * @param $name
     *
     * @return ProductOptions
     */
    public function getByName($name)
    {
        $model = $this->productOptionsFactory->create();
        $this->productOptionsResource->load($model, 2);

        return $model;
    }

    /**
     * @param int $itemId
     *
     * @return bool
     */
    public function deleteById($itemId)
    {
        $model = $this->get($itemId);
        $this->delete($model);

        return true;
    }
    
    public function deleteByProductId($productId)
    {
        $model = $this->productOptionsFactory->create();
        $this->productOptionsResource->load($model, $productId, 'product_id');
        $this->delete($model);
        return true;
    }

    /**
     * @param Data\ProductOptionsInterface $productOptionsInterface
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(Data\ProductOptionsInterface $productOptionsInterface)
    {
        try {
            $this->productOptionsResource->delete($productOptionsInterface);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to remove entity with ID%', $productOptionsInterface->getItemId())
            );
        }

        return true;
    }

    /**
     * @return array
     */
    public function getUnitsConfigFieldsLong()
    {
        return $this->unitsConfigFieldsLong;
    }

    /**
     * @return array
     */
    public function getUnitsConfigFieldsWeight()
    {
        return $this->unitsConfigFieldsWeight;
    }
}
