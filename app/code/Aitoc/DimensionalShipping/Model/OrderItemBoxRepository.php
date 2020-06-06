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

class OrderItemBoxRepository implements \Aitoc\DimensionalShipping\Api\OrderItemBoxRepositoryInterface
{
    protected $orderItemBoxModelResource;
    protected $orderItemBoxModelFactory;
    private $entities = [];

    /**
     * OrderItemBoxRepository constructor.
     *
     * @param ResourceModel\OrderItemBox $orderItemBoxModelResource
     * @param OrderItemBoxFactory        $orderItemBoxModelFactory
     */
    public function __construct(
        \Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox $orderItemBoxModelResource,
        \Aitoc\DimensionalShipping\Model\OrderItemBoxFactory $orderItemBoxModelFactory
    ) {
        $this->orderItemBoxModelResource = $orderItemBoxModelResource;
        $this->orderItemBoxModelFactory  = $orderItemBoxModelFactory;
    }

    /**
     * @param Data\OrderItemBoxInterface $orderItemBoxModel
     *
     * @return Data\OrderItemBoxInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\OrderItemBoxInterface $orderItemBoxModel)
    {
        if ($orderItemBoxModel->getItemId()) {
            $orderItemBoxModel = $this->get($orderItemBoxModel->getItemId())
                ->addData($orderItemBoxModel->getData());
        }
        try {
            $this->orderItemBoxModelResource->save($orderItemBoxModel);
            unset($this->entities);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $orderItemBoxModel->getItemId()));
        }

        return $orderItemBoxModel;
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
            /** var \Aitoc\DimensionalShipping\Model\Box $boxModel */
            $boxModel = $this->orderItemBoxModelFactory->create();
            $this->orderItemBoxModelResource->load($boxModel, $itemId);
            if (!$boxModel->getItemId()) {
                throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $itemId));
            }
            $this->entities[$itemId] = $boxModel;
        }

        return $this->entities[$itemId];
    }

    public function create()
    {
        $boxModel = $this->orderItemBoxModelFactory->create();

        return $boxModel;
    }

    /**
     * @param $orderItemId
     *
     * @return OrderItemBox
     */
    public function getByOrderItemId($orderItemId)
    {
        $model = $this->orderItemBoxModelFactory->create();
        $this->orderItemBoxModelResource->load($model, $orderItemId, 'order_item_id');
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
     * @param Data\OrderItemBoxInterface $orderItemBoxModel
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(Data\OrderItemBoxInterface $orderItemBoxModel)
    {
        try {
            $this->boxResource->delete($orderItemBoxModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to remove entity with ID "%1"', $orderItemBoxModel->getItemId())
            );
        }

        return true;
    }
}
