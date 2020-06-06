<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 17.09.19
 * Time: 15:05
 */

namespace KozakGroup\RewriteOrderEditor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class SendCommentOrderChange implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $oldItemsData = $observer->getData('oldItemsData');
        $oldOrderData = $observer->getData('oldOrderData');
        $newItemsData = $this->toArray($observer->getData('newItemsData'));
        foreach ($newItemsData as $key => $newItem) {
            $newItem['is_new'] = true;
            $newItemsDataFixed[$key] = $newItem;
        }
        $newOrderData = $observer->getData('newOrderData');
        $orderId = $newOrderData['entity_id'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
        $order->addStatusHistoryComment($this->compareItems($oldItemsData, $newItemsDataFixed) . '<br>', $order->getStatus());
        if($oldOrderData['grand_total'] != $newOrderData['grand_total']){
            $order->addStatusHistoryComment($this->compareOrders($oldOrderData,$newOrderData) , $order->getStatus());
        }
        $order->save();
    }

    protected function compareItems($oldItemsData, $newItemsData)
    {
        $itemsComment = '';

        $commonArray = $oldItemsData + $newItemsData;
        $commonIds = array_intersect(array_keys($oldItemsData), array_keys($newItemsData));
        foreach ($commonIds as $itemId) {
            $commonArray[$itemId] = $oldItemsData[$itemId] + $newItemsData[$itemId];
        }

        foreach ($commonArray as $item) {
            if (isset($item['is_old']) && isset ($item['is_new'])) {
                if ($item['is_new'] == true && $item['is_old'] == true) {
                    $newItem = $newItemsData[$item['item_id']];
                    if (($item['qty_ordered'] !== $newItem['qty_ordered']) || ($item['case_qty'] != $newItem['case_qty'])) {
                        $itemsComment .= '<b>Item ' . $item['name'] . '</b> was updated with following:' . '<br>';
                        if ($item['qty_ordered'] != $newItem['qty_ordered']){
                            $itemsComment .= '-Qty was update from ' . $item['qty_ordered'] . '-To-' . $newItem['qty_ordered'] . '<br>';
                            $itemsComment .= '-Unit Wgt. was update from ' . number_format((float)($item['weight']*$item['qty_ordered']), 4, '.', '') .
                                '-To-' . number_format((float)($newItem['weight']*$newItem['qty_ordered']), 4, '.', '') . '<br>';
                        }
                        if ($item['case_qty'] != $newItem['case_qty']){
                            $itemsComment .= '-Cases was update from ' . $item['case_qty'] . '-To-' . $newItem['case_qty'] . '<br>';
                        }
                        if ($item['tax_amount'] != $newItem['tax_amount']) {
                            $itemsComment .= '-Tax was update from $' . number_format($item['tax_amount'],2) . '-To-$' . $newItem['tax_amount'] . '<br>';
                        }
                        if ($item['row_total'] != $newItem['row_total']) {
                            $itemsComment .= '-Row Total was update from $' . number_format($item['row_total'],2) . '-To-$' . $newItem['row_total'] . '<br>';
                        }
                        if ($item['price'] != $newItem['price']) {
                            $itemsComment .= '-Price was update from $' . number_format($item['price'],2) . '-To-$' . $newItem['price'] . '<br>';
                        }
                    }
                }
            } elseif (isset($item['is_new'])) {
                if ($item['is_new'] == true && !isset($item['is_old'])) {
                    $itemsComment .= '<b>Item ' . $item['name'] . '</b> was added with following:' . '<br>';
                    $itemsComment .= '-Qty set to ' . $item['qty_ordered'] . '<br>';
                    $itemsComment .= '-Unit Wgt set to ' . number_format((float)($item['weight']*$item['qty_ordered']), 4, '.', '') . '<br>';
                    $itemsComment .= '-Cases set to ' . $item['case_qty'] . '<br>';
                    $itemsComment .= '-Tax set to $' . $item['tax_amount'] . '<br>';
                    $itemsComment .= '-Row Total set to $' . $item['row_total'] . '<br>';
                    $itemsComment .= '-Price set to $' . $item['price'] . '<br>';
                }
            } elseif ($item['is_old'] == true && !isset($item['is_new'])) {
                $itemsComment .= '<b>Item ' . $item['name'] . '</b> was deleted<br>';

            }
        }
        return $itemsComment;
    }

    protected function compareOrders($oldOrderData, $newOrderData)
    {
        $orderComment = '<b>Order Total:</b> <br>';

        $orderComment .= '-Order Subtotal(Excl. Tax) was update from $' . number_format($oldOrderData['subtotal'],2) . '-To-$' . $newOrderData['subtotal'] . '<br>';
        if($oldOrderData['shipping_amount'] != $newOrderData['shipping_amount']){
            $orderComment .= '-Order Shipping & Handling was update from $' . number_format($oldOrderData['shipping_amount'],2) . '-To-$' . $newOrderData['shipping_amount'] . '<br>';
        }
        $orderComment .= '-Order Grand Total was update from $' . number_format($oldOrderData['grand_total'],2) . '-To-$' . $newOrderData['grand_total'] . '<br>';
        if($oldOrderData['tax_amount'] != $newOrderData['tax_amount']) {
            $orderComment .= '-Tax was update from $' . number_format($oldOrderData['tax_amount'],2) . '-To-$' . $newOrderData['tax_amount'] . '<br>';
        }

        return $orderComment;
    }



    protected function toArray($items)
    {
        $data = [];
        foreach ($items as $item)
        {
            $data[$item->getId()] = $item->getData();
        }
        return $data;
    }

}