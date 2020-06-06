<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Show extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_handy;
    protected $_context;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ){
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_handy = $handy;
        $this->_context = $context;
    }

    public function execute()
    {
        $labelIds = $this->getRequest()->getParam('label_ids', '');

        $orderId = $this->getRequest()->getParam('order_id', null);
        $shipmentId = $this->getRequest()->getParam('shipment_id', null);
        $type = $this->getRequest()->getParam('type', null);

        $redirectPath = $this->getRequest()->getParam('redirect_path', null);

        $labels = [];
        $model = [];
        if ($labelIds !== '') {
            $labels = $this->_objectManager->get('Infomodus\Upslabel\Model\Items')->getCollection()->addOrder('created_time', 'DESC')->addFieldToFilter('upslabel_id', ['in' => explode(',', $labelIds)]);
            $redirectLink = $this->getUrl('infomodus_upslabel/*/');
            $model['back_link'] = $redirectLink;
        } elseif ($orderId !== null || $shipmentId !== null) {
            $labels = $this->_objectManager->get('Infomodus\Upslabel\Model\Items')->getCollection()->addOrder('created_time', 'DESC');
            if ($orderId !== null) {
                $labels->addFieldToFilter('order_id', $orderId);
            }

            if ($shipmentId !== null) {
                $labels->addFieldToFilter('shipment_id', $shipmentId);
            }

            if ($type !== null) {
                if ($type == 'shipment') {
                    $labels->addFieldToFilter('type', [['eq' => $type], ['eq' => 'invert']]);
                } else {
                    $labels->addFieldToFilter('type', $type);
                }
            }
        }

        if ($redirectPath !== null) {
            if (count($labels) > 0) {
                $firstLabel = $labels->getFirstItem();
                $isParams = true;
                switch ($redirectPath) {
                    case 'order':
                        $varName = 'Order';
                        $redirectPath = 'order';
                        $redirectPath2 = 'order/view';
                        break;
                    case 'order_list':
                        $varName = 'Order';
                        $redirectPath = 'order';
                        $redirectPath2 = 'order/index';
                        $isParams = false;
                        break;
                    case 'shipment_list':
                        $varName = 'Shipment';
                        $redirectPath = 'shipment';
                        $redirectPath2 = 'shipment/index';
                        $isParams = false;
                        break;
                    case 'refund_list':
                        $varName = 'Creditmemo';
                        $redirectPath = 'creditmemo';
                        $redirectPath2 = 'creditmemo/index';
                        $isParams = false;
                        break;
                    case 'refund':
                        $varName = 'Creditmemo';
                        $redirectPath = 'creditmemo';
                        $redirectPath2 = 'creditmemo/view';
                        break;
                    default:
                        $varName = 'Shipment';
                        $redirectPath = 'shipment';
                        $redirectPath2 = 'shipment/view';
                        break;
                }
                $params = [];
                if ($isParams) {
                    $params = [$redirectPath . '_id' => $firstLabel->{'get' . $varName . 'Id'}()];
                }

                $backLink = $this->getUrl('sales/' . $redirectPath2, $params);
                $model['back_link'] = $backLink;
            }
        }

        $model['labels'] = $labels;
        $labelTypes = [];
        if (!empty($labels)) {
            foreach ($labels as $label) {
                $labelTypes[] = $label->getType2();
            }

            if (!empty($labelTypes)) {
                $labelTypes = array_unique($labelTypes);
            }
            $model['label_types'] = $labelTypes;

            $this->coreRegistry->register('infomodus_upslabel_items_show', $model);
            $this->_initAction();
            $this->_view->getLayout()->getBlock('items_items_show');
            $this->_view->renderLayout();
        } else {
             $this->_redirect($this->getUrl('infomodus_upslabel/*'));
        }
    }
}
