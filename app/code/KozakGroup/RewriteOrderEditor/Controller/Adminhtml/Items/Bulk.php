<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 29.10.19
 * Time: 12:38
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Items;


class Bulk extends \Infomodus\Upslabel\Controller\Adminhtml\Items\Bulk
{

    public function massAction(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        $ids = ($collection->getAllIds()) ? $collection->getAllIds() : array($this->getRequest()->getParam('order_id'));
        foreach ($ids as $key=>$id){
            $order = $this->orderRepository->get($id);
            if($order->getTracksCollection()->getItems()){
                unset($ids[$key]);
            }
        }
        if (count($ids) > 0) {
            $isOrder = false;
            switch ($this->getRequest()->getParam('namespace')) {
                case 'sales_order_grid':
                    $type = 'shipment';
                    $isOrder = true;
                    $errorLink = 'sales/order';
                    break;
                case 'sales_order_shipment_grid':
                    $isOrder = true;
                    $type = 'shipment';
                    $errorLink = 'sales/shipment';
                    break;
                case 'sales_order_creditmemo_grid':
                    $type = 'refund';
                    $errorLink = 'sales/creditmemo';
                    break;
                default:
                    $type = 'shipment';
                    $isOrder = true;
                    $errorLink = 'infomodus_upslabel/items';
                    break;
            }
            $isOk = false;
            foreach ($ids as $id) {
                $handy = $this->handyFactory->create();
                if ($isOrder === true) {
                    $order = $this->orderRepository->get($id);
                    if($order->getShippingMethod() != "storepickup_storepickup"){
                        $storeId = $order->getStoreId();
                        $isShippingActiveMethods = $this->_conf
                            ->getStoreConfig('upslabel/bulk_create_labels/bulk_shipping_methods', $storeId);
                        if ($isShippingActiveMethods == 'specify') {
                            $shippingActiveMethods = trim($this->_conf
                                ->getStoreConfig('upslabel/bulk_create_labels/apply_to', $storeId), " ,");
                            $shippingActiveMethods = strlen($shippingActiveMethods) > 0 ?
                                explode(",", $shippingActiveMethods) : [];
                        }
                        $isOrderStatuses = $this->_conf
                            ->getStoreConfig('upslabel/bulk_create_labels/bulk_order_status', $storeId);
                        if ($isOrderStatuses == 'specify') {
                            $orderStatuses = explode(",", trim($this->_conf
                                ->getStoreConfig('upslabel/bulk_create_labels/orderstatus', $storeId),
                                " ,"));
                        }
                        if ((
                                $isShippingActiveMethods == 'all'
                                || (
                                    isset($shippingActiveMethods)
                                    && !empty($shippingActiveMethods)
                                    && in_array($order->getShippingMethod(), $shippingActiveMethods)
                                )
                            )
                            &&
                            (
                                $isOrderStatuses
                                ||
                                (
                                    isset($orderStatuses)
                                    && !empty($orderStatuses)
                                    && in_array($order->getStatus(), $orderStatuses)
                                )
                            )
                        ) {
                            $handy->intermediate($id, $type);
                            $handy->defConfParams['package'] = $handy->defPackageParams;
                            $handy->getLabel(null, $type, null, $handy->defConfParams);
                            $order->addStatusHistoryComment($this->addCommentCreateLabel($handy),$order->getStatus());
                            $order->save();
                        }
                    }
                } else {
                    $handy->intermediate(null, $type, $id);
                    $handy->defConfParams['package'] = $handy->defPackageParams;
                    $handy->getLabel(null, $type, $id, $handy->defConfParams);
                }

                if (count($handy->error) == 0) {
                    $isOk = true;
                } else {
                    $this->messageManager->addErrorMessage(__('For the selected items are not created labels.'));
                }

            }

            if ($isOk === true) {
                $this->messageManager->addSuccessMessage(__('For the selected items are created labels.'));
            }

            return $this->resultRedirectFactory->create()->setPath($errorLink . '/');
        }
    }

    protected function addCommentCreateLabel($handy){
        $labelComment = '<b>Created label:</b><br>';
        $labelComment .= '-Created tracking number: '. $handy->label[0]->getShipmentidentificationnumber() . '<br>';
        return $labelComment;
    }

}