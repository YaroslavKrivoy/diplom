<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Observer;

use Aitoc\DimensionalShipping\Helper\Data;
use Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;
use Aitoc\DimensionalShipping\Model\BoxRepository;
use Aitoc\DimensionalShipping\Model\OrderBoxRepository;
use Aitoc\DimensionalShipping\Model\OrderItemBoxRepository;
use Aitoc\DimensionalShipping\Model\ResourceModel\Box\CollectionFactory;
use Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions\CollectionFactory as DimensionalShippingProductOptionsCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\ItemRepository as OrderItemRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

/**
 * Class OrderPlaceAfter
 *
 * @package Aitoc\DimensionalShipping\Observer
 */
class OrderPlaceAfter implements ObserverInterface
{

    const SEPARATELY_EACH_ITEM = 1;
    const SEPARATELY_ONE_BOX_MORE_ITEMS = 2;
    const DIMENSIONS_ERROR_MESSAGE = 'The item(s) can\'t be packed due to excessive or unspecified dimensions and/or weight.';
    const NO_BOXES_ERROR_MESSAGE = 'The item(s) can\'t be packed as no box was created.';
    const DYNAMIC_WEIGHT_TYPE_ID = 0;

    protected $boxCollection;
    protected $orderRepository;
    protected $helper;
    protected $boxRepository;
    protected $packingModelFactory;
    protected $packingItemModelFactory;
    protected $boxModelFactory;
    protected $dimensionalShippingProductOptionsCollectionFactory;
    protected $orderItemBoxRepository;
    protected $orderBoxRepository;
    protected $orderItemRepository;
    protected $orderItemCollectionFactory;
    private $generalPackingModel;
    private $excludedItemsParentIds;

    /**
     * OrderPlaceAfter constructor.
     *
     * @param CollectionFactory                                  $boxCollection
     * @param OrderRepository                                    $orderRepository
     * @param BoxRepository                                      $boxRepository
     * @param Data                                               $helper
     * @param Boxpacker\PackerFactory                            $packingModelFactory
     * @param Boxpacker\TestItemFactory                          $packingItemModelFactory
     * @param Boxpacker\TestBoxFactory                           $boxModelFactory
     * @param DimensionalShippingProductOptionsCollectionFactory $dimensionalShippingProductOptionsCollectionFactory
     * @param OrderItemBoxRepository                             $orderItemBoxRepository
     * @param OrderBoxRepository                                 $orderBoxRepository
     * @param ItemRepository                                     $orderItemRepository
     * @param OrderItemCollectionFactory                         $orderItemCollectionFactory
     */
    public function __construct(
        CollectionFactory $boxCollection,
        OrderRepository $orderRepository,
        BoxRepository $boxRepository,
        Data $helper,
        Boxpacker\PackerFactory $packingModelFactory,
        Boxpacker\TestItemFactory $packingItemModelFactory,
        Boxpacker\TestBoxFactory $boxModelFactory,
        DimensionalShippingProductOptionsCollectionFactory $dimensionalShippingProductOptionsCollectionFactory,
        OrderItemBoxRepository $orderItemBoxRepository,
        OrderBoxRepository $orderBoxRepository,
        OrderItemRepository $orderItemRepository,
        OrderItemCollectionFactory $orderItemCollectionFactory
    ) {
        $this->boxCollection = $boxCollection;
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->boxRepository = $boxRepository;
        $this->packingModelFactory = $packingModelFactory;
        $this->packingItemModelFactory = $packingItemModelFactory;
        $this->boxModelFactory = $boxModelFactory;
        $this->dimensionalShippingProductOptionsCollectionFactory = $dimensionalShippingProductOptionsCollectionFactory;
        $this->orderItemBoxRepository = $orderItemBoxRepository;
        $this->orderBoxRepository = $orderBoxRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->generalPackingModel = $packingModelFactory->create();
        $this->excludedItemsParentIds = [];
    }

    /**
     * Box Packing Process.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->addBoxes();
        $orderIds = $observer->getEvent()->getOrderIds();
        foreach ($orderIds as $orderId) {
            $boxesCount = $this->boxCollection->create()->count();
            $order = $this->orderRepository->get($orderId);
            $orderItems = $order->getItems();
            foreach ($orderItems as $item) {
                $productOptions = $this->getProductOptionsByProductId($item->getProductId());
                $productType = $item->getProductType();

                // Check product types.
                if ($productType == 'downloadable') {
                    continue;
                } elseif (in_array($productType, ['bundle', 'configurable'])) {
                    if ($this->processCompositeProducts($item, $productOptions) === false) {
                        continue;
                    }
                }

                // Check if current item is a part of excluded composite product
                if ($item->getParentItemId() && in_array($item->getParentItemId(), $this->excludedItemsParentIds)) {
                    continue;
                }

                // Check box availability
                if ($boxesCount == 0) {
                    $this->saveItemWithError(self::NO_BOXES_ERROR_MESSAGE, $item);
                    continue;
                }

                //TODO: check item size and maximal box size

                // Check product dimensions
                if ($this->validateProductOptions($productOptions)) {
                    // Check product weight
                    if (!empty($item->getWeight())) {
                        if (!$this->helper->checkProductsType($item)) {
                            continue;
                        }
                        $productOptionsConverted = $this->helper->convertUnits($productOptions, 'item');
                        if ($productOptions->getSpecialBox()) {
                            $this->helper->saveProductsInBox(
                                $productOptions->getSelectBox(),
                                $item->getItemId(),
                                $orderId,
                                $item->getWeight(),
                                $item->getSku()
                            );
                        } elseif ($productOptions->getPackSeparately()) {
                            $this->packSeparatelyItem(
                                $item,
                                $orderId,
                                $productOptionsConverted
                            );
                        } else {
                            $this->packItem($item, $productOptionsConverted);
                        }
                    } else {
                        $this->saveItemWithError(self::DIMENSIONS_ERROR_MESSAGE, $item);
                    }
                } else {
                    $this->saveItemWithError(self::DIMENSIONS_ERROR_MESSAGE, $item);
                }
            }
            $packedBoxesAll = $this->generalPackingModel->pack();
            if ($packedBoxesAll) {
                $this->saveBoxesAndItems($packedBoxesAll, $orderId);
            }
        }
    }

    /**
     * Add all boxes data to packing model
     *
     * @param $packingModel
     */
    private function addBoxes(&$packingModel = false)
    {
        if (!$packingModel) {
            $packingModel = $this->generalPackingModel;
        }
        $boxes = $this->boxCollection->create()->getItems();
        foreach ($boxes as $box) {
            $convertedUnitsBox = $this->helper->convertUnits($box, 'box');
            $boxesModel = $this->boxModelFactory->create(
                [
                    'reference'   => $convertedUnitsBox->getName(),
                    'outerWidth'  => $convertedUnitsBox->getOuterWidth(),
                    'outerLength' => $convertedUnitsBox->getOuterLength(),
                    'outerDepth'  => $convertedUnitsBox->getOuterHeight(),
                    'emptyWeight' => $convertedUnitsBox->getEmptyWeight(),
                    'innerWidth'  => $convertedUnitsBox->getWidth(),
                    'innerLength' => $convertedUnitsBox->getLength(),
                    'innerDepth'  => $convertedUnitsBox->getHeight(),
                    'maxWeight'   => $convertedUnitsBox->getWeight(),
                    'boxId'       => $convertedUnitsBox->getId(),
                    'weights' => [
                        1 => $convertedUnitsBox->getOneDayWeight(),
                        2 => $convertedUnitsBox->getTwoDayWeight(),
                        3 => $convertedUnitsBox->getThreeDayWeight(),
                        4 => $convertedUnitsBox->getFourDayWeight()
                    ]
                ]
            );
            $packingModel->addBox($boxesModel);
        }
    }

    /**
     * Pack order items separately
     *
     * @param $item
     * @param $orderId
     * @param $separatelyType
     * @param $productOptions
     */
    private function packSeparatelyItem($item, $orderId, $productOptions)
    {
        $separatelyType = $productOptions->getPackSeparately();
        $qtyIncrement = 0;
        $packingModel = $this->packingModelFactory->create();
        $this->addBoxes($packingModel);
        while ($qtyIncrement < $item->getQtyOrdered()) {
            $packingModelIndividual = $this->packingModelFactory->create();
            $this->addBoxes($packingModelIndividual);
            $itemModel = $this->packingItemModelFactory->create(
                [
                    'description' => $item->getName(),
                    'width'       => $productOptions->getWidth(),
                    'length'      => $productOptions->getLength(),
                    'depth'       => $productOptions->getHeight(),
                    'weight'      => $item->getWeight(),
                    'keepFlat'    => 0,
                    'orderItemId' => $item->getItemId()
                ]
            );
            $packingModelIndividual->addItem($itemModel);
            $packingModel->addItem($itemModel);
            $qtyIncrement++;
            if ($separatelyType == $this::SEPARATELY_EACH_ITEM) {
                $packedBoxes = $packingModelIndividual->pack();
                $this->saveBoxesAndItems($packedBoxes, $orderId);
            }
        }
        if ($separatelyType == $this::SEPARATELY_ONE_BOX_MORE_ITEMS) {
            $packedBoxes = $packingModel->pack();
            $this->saveBoxesAndItems($packedBoxes, $orderId);
        }
    }

    /**
     * Add order item to general packing model
     *
     * @param $item
     * @param $options
     */
    private function packItem($item, $options)
    {
        $qtyIncrement = 0;
        while ($qtyIncrement < $item->getQtyOrdered()) {
            $itemModel = $this->packingItemModelFactory->create(
                [
                    'description' => $item->getName(),
                    'width'       => $options->getWidth(),
                    'length'      => $options->getLength(),
                    'depth'       => $options->getHeight(),
                    'weight'      => $item->getProduct()->getWeight(),
                    'keepFlat'    => 0,
                    'orderItemId' => $item->getItemId()
                ]
            );
            $this->generalPackingModel->addItem($itemModel);
            $qtyIncrement++;
        }
    }

    /**
     * Save packing result from packing model to database
     *
     * @param $packedBoxesAll
     * @param $orderId
     */
    private function saveBoxesAndItems($packedBoxesAll, $orderId)
    {
        if ($packedBoxesAll) {
            foreach ($packedBoxesAll as $packedBox) {
                $boxType = $packedBox->getBox();
                $itemsInTheBox = $packedBox->getItems();

                $orderBoxModel = $this->orderBoxRepository->create();
                $orderBoxModel->setOrderId($orderId)
                    ->setBoxId($boxType->getBoxId())
                    ->setWeight($packedBox->getWeight());
                $orderBoxModel = $this->orderBoxRepository->save($orderBoxModel);

                foreach ($itemsInTheBox as $item) {
                    $orderItemBoxModel = $this->orderItemBoxRepository->create();
                    $itemModel = $this->orderItemRepository->get($item->getOrderItemId());
                    $orderItemBoxModel->setOrderItemId($item->getOrderItemId())
                        ->setOrderBoxId($orderBoxModel->getItemId())
                        ->setOrderId($orderId)
                        ->setSku($itemModel->getSku())
                        ->setNotPacked(0);
                    $this->orderItemBoxRepository->save($orderItemBoxModel);
                }
            }
        }
    }

    /**
     * Mark order item as 'not packed' and save to database with error message
     *
     * @param $packedBoxesAll
     * @param $orderId
     */
    private function saveItemWithError($error, $item)
    {
        $qtyIncrement = 0;
        while ($qtyIncrement < $item->getQtyOrdered()) {
            $orderItemBoxModel = $this->orderItemBoxRepository->create();
            $itemModel = $this->orderItemRepository->get($item->getItemId());
            $orderItemBoxModel->setOrderItemId($itemModel->getItemId())
                ->setOrderId($itemModel->getOrderId())
                ->setSku($itemModel->getSku())
                ->setErrorMessage($error)
                ->setNotPacked(true);
            $this->orderItemBoxRepository->save($orderItemBoxModel);
            $qtyIncrement++;
        }
    }

    /**
     * Check child items setting(dimensions/wight) from composite order items
     *
     * @param $childItems
     */
    private function checkChildItemsWeight($childItems)
    {
        foreach ($childItems as $childItem) {
            $productOptions = $this->getProductOptionsByProductId($childItem->getProductId());
//            if (!$this->validateProductOptions($productOptions)) {
//                return false;
//            }
            if (!$childItem->getWeight()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate product dimensions
     *
     * @param $productOptions
     */
    private function validateProductOptions($productOptions)
    {
        $productDimensionalFields = $this->helper->getProductOptionsModelFields('long');
        foreach ($productDimensionalFields as $field) {
            $field = 'get' . $field;
            $data = $productOptions->{$field}();
            if (empty($data) || $data < 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get product options by product id
     *
     * @param $productId
     */
    private function getProductOptionsByProductId($productId)
    {
        $dSOptionsCollection = $this->dimensionalShippingProductOptionsCollectionFactory->create();
        return $dSOptionsCollection->addFieldToFilter('product_id', $productId)->getFirstItem();
    }

    /**
     * Process packing for composite products (bundle, configurable)
     *
     * @param $item
     * @param $productOptions
     */
    private function processCompositeProducts($item, $productOptions)
    {
        if (!$this->validateProductOptions($productOptions)) {
            return false;
        }

        switch ($item->getProductType()) {
            case 'bundle':
                //check weight type for bundle: 0 - dynamic weight; 1 - static weight
                if ($item->getProduct()->getWeightType() == self::DYNAMIC_WEIGHT_TYPE_ID) {
                    $orderItemCollection = $this->orderItemCollectionFactory->create()
                        ->addFieldToFilter('parent_item_id', $item->getItemId())
                        ->getItems();
                    // Check if all child items contain weight
                    if ($this->checkChildItemsWeight($orderItemCollection)) {
                        // exclude child items from packing
                        $this->excludedItemsParentIds[] = $item->getItemId();
                    } else {
                        // exclude bundle item and pack only child items
                        return false;
                    }
                } else {
                    // check if bundle item has weight param
                    if (!empty($item->getWeight())) {
                        // exclude child items from packing
                        $this->excludedItemsParentIds[] = $item->getItemId();
                    } else {
                        // exclude bundle item and pack only child items
                        return false;
                    }
                }
                break;
            case 'configurable':
                // check if configurable product has weight param
                if (!empty($item->getProduct()->getWeight())) {
                    $this->excludedItemsParentIds[] = $item->getItemId();
                } else {
                    // exclude configurable item and pack only child items
                    return false;
                }
                break;
        }
        return true;
    }
}
