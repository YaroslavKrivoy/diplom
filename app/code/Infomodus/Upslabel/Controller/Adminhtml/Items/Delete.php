<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Delete extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $handy;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->handy = $handy;
    }

    public function execute()
    {
        $redirect = $this->getRequest()->getParam('redirect', 1);
        try {
            $shipmentId = null;
            $orderId = null;

            $shipidnumber = $this->getRequest()->getParam('shipidnumber', null);
            $type = 'shipidnumber';
            if ($shipidnumber !== null) {
                if (!is_array($shipidnumber)) {
                    $shipidnumber[0] = $shipidnumber;
                    $this->messageManager->addSuccessMessage(__('You deleted the item(s).'));
                } else {
                    $shipidnumber = array_unique($shipidnumber);
                }

                if ($this->handy->deleteLabel($shipidnumber, $type)) {
                    if ($shipmentId === null) {
                        $shipmentId = $this->handy->shipmentId;
                    }

                    if ($orderId === null) {
                        $orderId = $this->handy->order->getId();
                    }
                }
            }

            $type = 'label_ids';
            $shipidnumber = $this->getRequest()->getParam('label_ids', null);
            if ($shipidnumber !== null) {
                if (!is_array($shipidnumber)) {
                    $shipidnumber[0] = $shipidnumber;
                    $this->messageManager->addSuccessMessage(__('You deleted the item(s).'));
                } else {
                    $shipidnumber = array_unique($shipidnumber);
                }

                if ($this->handy->deleteLabel($shipidnumber, $type)) {
                    if ($shipmentId === null) {
                        $shipmentId = $this->handy->shipmentId;
                    }

                    if ($orderId === null) {
                        $orderId = $this->handy->order->getId();
                    }
                }
            }

            if ($redirect == 1) {
                $this->_redirect('infomodus_upslabel/items/index');
            } else {
                if ($shipmentId !== null || $orderId !== null) {
                    $redirectPath = $this->getRequest()->getParam('redirect_path');
                    if ($redirectPath == 'order' || $redirectPath == 'order_list') {
                        $this->_redirect('sales/order/view', ['order_id' => $orderId]);
                    } elseif ($redirectPath == 'shipment' || $redirectPath == 'shipment_list') {
                        $this->_redirect('sales/shipment/view', ['shipment_id' => $shipmentId]);
                    } elseif ($redirectPath == 'creditmemo' || $redirectPath == 'creditmemo_list') {
                        $this->_redirect('sales/creditmemo/view', ['creditmemo_id' => $shipmentId]);
                    } else {
                        $this->_redirect('infomodus_upslabel/items/index');
                    }
                } else {
                    $this->_redirect($this->_redirect->getRefererUrl());
                }
            }

            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t delete item right now. Please review the log and try again.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            if ($redirect == 1) {
                $this->_redirect('infomodus_upslabel/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            } else {
                $this->_redirect('infomodus_upslabel/*/');
            }

            return;
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));
        $this->_redirect('infomodus_upslabel/*/');
    }
}
