<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Save extends \Infomodus\Upslabel\Controller\Adminhtml\Items
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
        if ($this->getRequest()->getPostValue()) {
            $orderId = $this->getRequest()->getParam('order_id');
            $type = $this->getRequest()->getParam('type');
            $shipmentId = $this->getRequest()->getParam('shipment_id', null);
            $redirectUrl = $this->getRequest()->getParam('redirect_path', null);
            $params = $this->getRequest()->getParams();
            $order = $this->_objectManager->get('Magento\Sales\Model\OrderRepository')->get($orderId);
            if ($order) {
                if (isset($params['package'])) {
                    $arrPackagesOld = $params['package'];
                    $arrPackages = [];
                    if (count($arrPackagesOld) > 0) {
                        foreach ($arrPackagesOld as $k => $v) {
                            $i = 0;
                            foreach ($v as $d => $f) {
                                $arrPackages[$i][$k] = $f;
                                $i++;
                            }
                        }
                        unset($v, $k, $i, $d, $f);
                        $params['package'] = $arrPackages;
                    }

                    $label = $this->_handy->getLabel($order, $type, $shipmentId, $params);
                    if (true === $label) {
                        $labelIds = [];
                        if (count($this->_handy->label) > 0) {
                            foreach ($this->_handy->label as $label) {
                                $labelIds[] = $label->getId();
                            }
                        }
                        if (count($this->_handy->label2) > 0) {
                            foreach ($this->_handy->label2 as $label2) {
                                $labelIds[] = $label2->getId();
                            }
                        }
                        $this->_redirect(
                            'infomodus_upslabel/items/show',
                            [
                                'label_ids' => implode(',', $labelIds),
                                'type' => $type,
                                'redirect_path' => $redirectUrl
                            ]
                        );
                        return;
                    } else {
                        $this->messageManager->addErrorMessage(__('Error 10002.'));
                        $this->_redirect('infomodus_upslabel/*');
                        return;
                    }
                } else {
                    $this->messageManager
                        ->addErrorMessage(__('There must be at least one package. Add the package please'));
                    $this->_redirect('infomodus_upslabel/*');
                    return;
                }
            } else {
                $this->messageManager
                    ->addErrorMessage(__('Order is wrong'));
                $this->_redirect('infomodus_upslabel/*');
                return;
            }
        }

        $this->_redirect('infomodus_upslabel/*/');
    }
}
