<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OrdersBase\Api\Data\DeviceDataInterface;
use MageWorx\OrdersBase\Model\DeviceDataFactory;
use MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface;
use MageWorx\OrdersBase\Model\ResourceModel\DeviceData as ResourceDeviceData;
use MageWorx\OrdersBase\Model\ResourceModel\DeviceData\CollectionFactory as DeviceDataCollectionFactory;

class DeviceDataRepository implements DeviceDataRepositoryInterface
{
    /**
     * @var ResourceDeviceData
     */
    protected $resource;

    /**
     * @var DeviceDataFactory
     */
    protected $deviceDataFactory;

    /**
     * @var DeviceDataCollectionFactory
     */
    protected $deviceDataCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceDeviceData $resource
     * @param DeviceDataFactory $deviceDataFactory
     * @param DeviceDataCollectionFactory $deviceDataCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceDeviceData $resource,
        DeviceDataFactory $deviceDataFactory,
        DeviceDataCollectionFactory $deviceDataCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->deviceDataFactory = $deviceDataFactory;
        $this->deviceDataCollectionFactory = $deviceDataCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save DeviceData data
     *
     * @param DeviceData $deviceData
     * @return DeviceData
     * @throws CouldNotSaveException
     */
    public function save(DeviceData $deviceData)
    {
        try {
            /** @var \MageWorx\OrdersBase\Model\DeviceData $deviceData */
            $this->resource->save($deviceData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the deviceData: %1',
                    $exception->getMessage()
                )
            );
        }

        return $deviceData;
    }

    /**
     * Load DeviceData by given DeviceData Identity
     *
     * @param string $deviceDataId
     * @return DeviceData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($deviceDataId)
    {
        /** @var DeviceData $deviceData */
        $deviceData = $this->deviceDataFactory->create();
        $this->resource->load($deviceData, $deviceDataId);
        if (!$deviceData->getId()) {
            throw new NoSuchEntityException(__('DeviceData with id "%1" does not exist.', $deviceDataId));
        }

        return $deviceData;
    }
    
    /**
     * Load DeviceData collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface|ResourceDeviceData\Collection
     */
    public function getList(SearchCriteriaInterface $criteria, $returnRawObjects = false)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\OrdersBase\Model\ResourceModel\DeviceData\Collection $collection */
        $collection = $this->deviceDataCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter(
                    $filter->getField(),
                    [
                        $condition => $filter->getValue(),
                    ]
                );
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $deviceData = [];
        /** @var DeviceData $deviceDataModel */
        foreach ($collection as $deviceDataModel) {
            if ($returnRawObjects) {
                $deviceData[] = $deviceDataModel;
            } else {
                /** @var DeviceDataInterface $deviceDataData */
                $deviceDataData = $this->deviceDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $deviceDataData,
                    $deviceDataModel->getData(),
                    'MageWorx\OrdersBase\Api\Data\DeviceDataInterface'
                );
                $deviceData[] = $this->dataObjectProcessor->buildOutputDataArray(
                    $deviceDataData,
                    'MageWorx\OrdersBase\Api\Data\DeviceDataInterface'
                );
            }
        }
        $searchResults->setItems($deviceData);

        return $searchResults;
    }

    /**
     * Delete DeviceData
     *
     * @param DeviceData $deviceData
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DeviceData $deviceData)
    {
        try {
            $this->resource->delete($deviceData);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the device data: %1',
                    $exception->getMessage()
                )
            );
        }

        return true;
    }

    /**
     * Delete DeviceData by given DeviceData Identity
     *
     * @param string $deviceDataId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($deviceDataId)
    {
        return $this->delete($this->getById($deviceDataId));
    }

    /**
     * Get empty DeviceData
     *
     * @return DeviceData|\MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function getEmptyEntity()
    {
        /** @var DeviceData $deviceData */
        $deviceData = $this->deviceDataFactory->create();

        return $deviceData;
    }

    /**
     * Retrieve device data.
     *
     * @param int $orderId
     * @return DeviceData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOrderId($orderId)
    {
        /** @var DeviceData $deviceData */
        $deviceData = $this->deviceDataFactory->create();
        $this->resource->load($deviceData, $orderId, 'order_id');
        if (!$deviceData->getId()) {
            throw new NoSuchEntityException(__('Device Data for the Order with id "%1" does not exist.', $orderId));
        }

        return $deviceData;
    }
}
