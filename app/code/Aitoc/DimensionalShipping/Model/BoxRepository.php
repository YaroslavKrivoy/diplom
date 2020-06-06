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

class BoxRepository implements \Aitoc\DimensionalShipping\Api\BoxRepositoryInterface
{
    protected $boxModelResource;
    protected $boxModelFactory;
    private $entities = [];
    private $unitsConfigFieldsLong = [];
    private $unitsConfigFieldsWeight = [];

    /**
     * BoxRepository constructor.
     *
     * @param ResourceModel\Box $boxModelResource
     * @param BoxFactory        $boxModelFactory
     */
    public function __construct(
        \Aitoc\DimensionalShipping\Model\ResourceModel\Box $boxModelResource,
        \Aitoc\DimensionalShipping\Model\BoxFactory $boxModelFactory
    ) {
        $this->unitsConfigFieldsLong   = ['width', 'outer_width', 'height', 'outer_height', 'length', 'outer_length'];
        $this->unitsConfigFieldsWeight = ['weight', 'empty_weight'];
        $this->boxModelResource        = $boxModelResource;
        $this->boxModelFactory         = $boxModelFactory;
    }

    /**
     * @param Data\BoxInterface $boxModel
     *
     * @return Data\BoxInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\BoxInterface $boxModel)
    {
        if ($boxModel->getItemId()) {
            $boxModel = $this->get($boxModel->getItemId())
                ->addData($boxModel->getData());
        }
        try {
            $this->boxModelResource->save($boxModel);
            unset($this->entities);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $boxModel->getItemId()));
        }

        return $boxModel;
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
            $boxModel = $this->boxModelFactory->create();
            $this->boxModelResource->load($boxModel, $itemId);
            if (!$boxModel->getItemId()) {
                return false;
            }
            $this->entities[$itemId] = $boxModel;
        }

        return $this->entities[$itemId];
    }

    public function create()
    {
        $model = $this->boxModelFactory->create();

        return $model;
    }

    /**
     * @param $name
     *
     * @return Box
     */
    public function getByName($name)
    {
        $model = $this->boxModelFactory->create();
        $this->boxModelResource->load($model, 2);

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

        if ($this->delete($model)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Data\BoxInterface $boxModel
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(Data\BoxInterface $boxModel)
    {
        try {
            $this->boxModelResource->delete($boxModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID "%1"', $boxModel->getItemId()));
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
