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

class OrderBoxRepository implements \Aitoc\DimensionalShipping\Api\OrderBoxRepositoryInterface
{
    protected $orderBoxModelResource;
    protected $orderBoxModelFactory;
    private $entities = [];

    /**
     * OrderBoxRepository constructor.
     *
     * @param ResourceModel\OrderBox $orderBoxModelResource
     * @param OrderBoxFactory        $orderBoxModelFactory
     */
    public function __construct(
        \Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox $orderBoxModelResource,
        \Aitoc\DimensionalShipping\Model\OrderBoxFactory $orderBoxModelFactory
    ) {
        $this->orderBoxModelResource = $orderBoxModelResource;
        $this->orderBoxModelFactory  = $orderBoxModelFactory;
    }

    /**
     * @param Data\OrderBoxInterface $orderBoxModel
     *
     * @return Data\OrderBoxInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\OrderBoxInterface $orderBoxModel)
    {
        if ($orderBoxModel->getItemId()) {
            $orderBoxModel = $this->get($orderBoxModel->getItemId())
                ->addData($orderBoxModel->getData());
        }
        try {
            $this->orderBoxModelResource->save($orderBoxModel);
            unset($this->entities);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $orderBoxModel->getItemId()));
        }

        return $orderBoxModel;
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
            $boxModel = $this->orderBoxModelFactory->create();
            $this->orderBoxModelResource->load($boxModel, $itemId);
            if (!$boxModel->getItemId()) {
                throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $itemId));
            }
            $this->entities[$itemId] = $boxModel;
        }

        return $this->entities[$itemId];
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $boxModel = $this->orderBoxModelFactory->create();

        return $boxModel;
    }

    /**
     * @param $orderItemId
     *
     * @return mixed
     */
    public function getByOrderItemId($orderItemId)
    {
        $model = $this->orderBoxModelFactory->create();
        $this->orderBoxModelResource->load($model, $orderItemId, 'order_item_id');
        $this->entities[$orderItemId] = $model;

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

    /**
     * @param Data\OrderBoxInterface $orderBoxModel
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(Data\OrderBoxInterface $orderBoxModel)
    {
        try {
            $this->orderBoxModelResource->delete($orderBoxModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID "%1"', $orderBoxModel->getItemId()));
        }

        return true;
    }
}
