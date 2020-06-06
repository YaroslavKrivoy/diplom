<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model\Wbtab\Plugin\Model\ResourceModel;

use Magento\Sales\Model\Order as OrderModel;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Aheadworks\Autorelated\Model\Wbtab\Indexer\Product\Processor as IndexProcessor;

/**
 * Class Order
 * @package Aheadworks\Autorelated\Model\Wbtab\Plugin\Model\ResourceModel
 */
class Order
{
    /**
     * @var string[]|null
     */
    private $orderState;

    /**
     * @var IndexProcessor
     */
    private $indexProcessor;

    /**
     * @param IndexProcessor $indexProcessor
     */
    public function __construct(
        IndexProcessor $indexProcessor
    ) {
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * Store order status
     *
     * @param OrderResource $subject
     * @param \Closure $proceed
     * @param OrderModel $order
     * @param string $value
     * @param null $field
     * @return OrderResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        OrderResource $subject,
        \Closure $proceed,
        OrderModel $order,
        $value,
        $field = null
    ) {
        $result = $proceed($order, $value, $field);

        if ($order->getId()) {
            $this->orderState[$order->getId()] = $order->getState();
        }

        return $result;
    }

    /**
     * Check order to order history
     *
     * @param OrderResource $subject,
     * @param \Closure $proceed
     * @param OrderModel $order
     * @return OrderResource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        OrderResource $subject,
        \Closure $proceed,
        OrderModel $order
    ) {
        $result = $proceed($order);

        if ($order->getId()
            && isset($this->orderState[$order->getId()])
            && $this->orderState[$order->getId()] != $order->getState()
            && $order->getState() == OrderModel::STATE_COMPLETE
        ) {
            if ($order->getTotalItemCount() > 1) {
                if ($this->indexProcessor->isIndexerScheduled()) {
                    $this->indexProcessor->markIndexerAsInvalid();
                } else {
                    $ids = [];
                    /** @var \Magento\Sales\Model\Order\Item $item */
                    foreach ($order->getAllVisibleItems() as $item) {
                        $ids[] = $item->getProductId();
                    }
                    $this->indexProcessor->reindexList($ids);
                }
            }
        }
        return $result;
    }
}
