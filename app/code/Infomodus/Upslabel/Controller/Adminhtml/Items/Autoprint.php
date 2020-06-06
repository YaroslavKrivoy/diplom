<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Autoprint extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_handy;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_handy = $handy;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $shipmentId = $this->getRequest()->getParam('shipment_id', null);
        $labelId = $this->getRequest()->getParam('label_id', null);
        $shipident_id = $this->getRequest()->getParam('shipident_id', null);
        $type = $this->getRequest()->getParam('type');
        $type_print = $this->getRequest()->getParam('type_print', 'auto');
        $path = $this->_handy->_conf->getBaseDir('media') . '/upslabel/label/';

        $order = $this->_objectManager->get('Magento\Sales\Model\OrderRepository')->get($orderId);
        $storeId = $order->getStoreId();

        $upslabel = $this->_objectManager->get('Infomodus\Upslabel\Model\Items');
        if ($type_print === 'auto') {
            if ($labelId === null) {
                $colls2 = $upslabel->getCollection()->addFieldToFilter('order_id', $orderId);
                if ($shipmentId !== null) {
                    $colls2->addFieldToFilter('shipment_id', $shipmentId);
                }
                $colls2->addFieldToFilter('type', $type)->addFieldToFilter('lstatus', 0);
                foreach ($colls2 as $coll) {
                    if (file_exists($path . ($coll->getLabelname()))) {
                        if ($data = file_get_contents($path . ($coll->getLabelname()))) {
                            $this->_handy->_conf->sendPrint($data, $storeId);
                        }
                    }
                }
                $this->getResponse()
                    ->setContent(__('Label was sent to print'));
            } elseif ($shipident_id !== null) {
                $colls2 = $upslabel->getCollection()->addFieldToFilter('shipmentidentificationnumber', $shipident_id);
                $colls2->addFieldToFilter('lstatus', 0);
                foreach ($colls2 as $coll) {
                    if (file_exists($path . ($coll->getLabelname()))) {
                        if ($data = file_get_contents($path . ($coll->getLabelname()))) {
                            $this->_handy->_conf->sendPrint($data, $storeId);
                        }
                    }
                }
                $this->getResponse()
                    ->setContent(__('Label was sent to print'));
            } elseif ($labelId !== null) {
                $label = $upslabel->load($labelId);
                if ($label && !empty($label->getData()) && file_exists($path . ($label->getLabelname()))) {
                    if ($data = file_get_contents($path . ($label->getLabelname()))) {
                        $this->_handy->_conf->sendPrint($data, $storeId);
                    }
                    $this->getResponse()
                        ->setContent(__('Label was sent to print'));
                } else {
                    $this->getResponse()
                        ->setContent(__('Error'));
                }

            }
        } else {
            $label = $upslabel->load($labelId);
            $this->getResponse()
                ->setContent(file_get_contents($path.$label->getLabelname()));
        }
    }
}
