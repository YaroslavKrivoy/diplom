<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Orderitems;

/**
 * Class Changebox
 *
 * @package Aitoc\DimensionalShipping\Controller\Adminhtml\Orderitems
 */
class Changebox extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;

    protected $resultRawFactory;

    protected $orderItemBoxrepository;

    protected $orderBoxRepository;

    protected $orderItemBoxCollectionFactory;

    protected $orderBoxCollectionFactory;

    protected $itemRepository;

    protected $orderItemCollectionFactory;

    /**
     * Changebox constructor.
     *
     * @param \Magento\Framework\App\Action\Context                   $context
     * @param \Magento\Framework\Controller\Result\JsonFactory        $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory         $resultRawFactory
     * @param \Aitoc\DimensionalShipping\Model\OrderItemBoxRepository $orderItemBoxrepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Aitoc\DimensionalShipping\Model\OrderItemBoxRepository $orderItemBoxrepository,
        \Aitoc\DimensionalShipping\Model\OrderBoxRepository $orderBoxRepository,
        \Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox\CollectionFactory $orderBoxCollectionFactory,
        \Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox\CollectionFactory $orderItemBoxCollectionFactory,
        \Magento\Sales\Model\Order\ItemRepository $itemRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
    ) {
        $this->resultJsonFactory             = $resultJsonFactory;
        $this->resultRawFactory              = $resultRawFactory;
        $this->orderItemBoxrepository        = $orderItemBoxrepository;
        $this->orderBoxRepository            = $orderBoxRepository;
        $this->orderItemBoxCollectionFactory = $orderItemBoxCollectionFactory;
        $this->orderBoxCollectionFactory     = $orderBoxCollectionFactory;
        $this->itemRepository                = $itemRepository;
        $this->orderItemCollectionFactory    = $orderItemCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRaw          = $this->resultRawFactory->create();
        $httpBadRequestCode = 400;
        if ($this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        $orderItemBoxId = $this->getRequest()->getParam('order_item_box_id');
        $orderItemId    = $this->getRequest()->getParam('order_item_id');
        $sku            = $this->getRequest()->getParam('sku');
        $orderBoxId     = $this->getRequest()->getParam('order_box_id');
        $newBox         = $this->getRequest()->getParam('new_element');
        $orderId        = $this->getRequest()->getParam('order_id');
        $qty            = $this->getRequest()->getParam('qty_boxed');
        $notPacked      = $this->getRequest()->getParam('not_packed');
        if ($newBox == 1) {
            $orderBoxModel = $this->orderBoxRepository->create();
            $orderBoxModel->setOrderId($orderId);
            $orderBoxModel->setBoxId($orderBoxId);
            $newOrderBox = $this->orderBoxRepository->save($orderBoxModel);
        }
        if (!$notPacked) {
            if ($qty > 1) {
                $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create();
                $condition              = "`order_id`= {$orderId} AND `sku`= '{$sku}'";
                $orderItemBoxCollection->setTableRecords(
                    $condition,
                    ['order_box_id' => $newBox == 1 ? $newOrderBox->getItemId() : $orderBoxId],
                    $qty
                )->getItems();
            } else {
                if (!$orderItemId || !$orderBoxId) {
                    return $resultRaw->setHttpResponseCode($httpBadRequestCode);
                }
                $orderItemModel = $this->orderItemBoxrepository->get($orderItemBoxId);
                $orderItemModel->setOrderBoxId($newBox == 1 ? $newOrderBox->getItemId() : $orderBoxId)->save();
            }
        } else {
            if (!$qty) {
                $qty = 1;
            }
            $itemCollectionCount = $this->orderItemBoxCollectionFactory
                ->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('sku', $sku)
                ->addFieldToFilter('not_packed', 1)
                ->count();
            if ($qty > round($itemCollectionCount)) {
                $response   = [
                    'errors'  => true,
                    'message' => __('The qty is incorrect.')
                ];
                $resultJson = $this->resultJsonFactory->create();

                return $resultJson->setData($response);
            }
            $orderItemBoxCollection = $this->orderItemBoxCollectionFactory
                ->create()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('sku', $sku)
                ->addFieldToFilter('not_packed', 1)
                ->setLimit($qty)
                ->getItems();

            foreach ($orderItemBoxCollection as $orderItemBox) {
                $orderItemModel = $this->orderItemBoxrepository->get($orderItemBox->getId());
                $orderItemModel->setOrderBoxId($newBox == 1 ? $newOrderBox->getItemId() : $orderBoxId);
                $orderItem = $this->itemRepository->get($orderItemId);
                $orderItemModel->setNotPacked(0);
                $this->orderItemBoxrepository->save($orderItemModel);
            }
        }
        $response   = [
            'errors'  => false,
            'message' => __('Box changed successful.')
        ];
        $resultJson = $this->resultJsonFactory->create();

        //Check the boxes for the number of elements in them, if empty box do delete
        $orderBoxCollection = $this->orderBoxCollectionFactory->create();
        $orderBoxCollection->addFieldToFilter('order_id', $orderId)->getItems();
        foreach ($orderBoxCollection as $boxOrder) {
            $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create();
            $countItemsInBox        = $orderItemBoxCollection
                ->addFieldToFilter('order_box_id', $boxOrder->getId())
                ->addFieldToFilter('order_id', $orderId)
                ->count();
            if ($countItemsInBox == 0) {
                $this->orderBoxRepository->deleteById($boxOrder->getId());
            }
        }

        //Recalculate weight in all boxes
        $orderBoxCollection = $this->orderBoxCollectionFactory->create();
        $orderBoxCollection
            ->addFieldToFilter('order_id', $orderId)
            ->getItems();

        foreach ($orderBoxCollection as $box) {
            $weightBox              = 0;
            $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create();
            $orderItemBoxCollection->addFieldToFilter('order_box_id', $box->getId())->getItems();
            foreach ($orderItemBoxCollection as $orderItemBox) {
                $orderItemModel = $this->itemRepository->get($orderItemBox->getOrderItemId());
                $weightBox      += $orderItemModel->getWeight();
            }
            $orderItemBoxModel = $this->orderBoxRepository->get($box->getId());
            $orderItemBoxModel->setWeight($weightBox);
            $this->orderBoxRepository->save($orderItemBoxModel);
        }

        return $resultJson->setData($response);
    }
}
