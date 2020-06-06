<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Price extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    protected $_handy;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    )
    {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_handy = $handy;
    }

    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $orderId = $this->getRequest()->getParam('order_id');
            $type = $this->getRequest()->getParam('type');
            $params = $this->getRequest()->getParams();
            $order = $this->_objectManager->get('Magento\Sales\Model\OrderRepository')->get($orderId);

            $arrPackagesOld = $params['package'];
            $arrPackages = [];
            if (count($arrPackagesOld) > 0) {
                foreach ($arrPackagesOld as $k => $v) {
                    $i = 0;
                    foreach ($v as $d => $f) {
                        $arrPackages[$i][$k] = $f;
                        $i += 1;
                    }
                }
                unset($v, $k, $i, $d, $f);
                $params['package'] = $arrPackages;
            }

            $price = $this->_handy->getLabel($order, 'ajaxprice_' . $type, null, $params);
            $content = '';
            if (!is_array($price) && strlen($price) > 0) {
                $price = json_decode($price, true);
                if (isset($price['price'])) {
                    $content .= __('Price') . ': ' . $price['price']['def']['MonetaryValue'] . '' . $price['price']['def']['CurrencyCode'];
                    if (isset($price['price']['negotiated']) && is_array($price['price']['negotiated']) && count($price['price']['negotiated']) > 0) {
                        $content .= '<br />' . __('Negotiated Price') . ': ' . $price['price']['negotiated']['MonetaryValue'] . '' . $price['price']['negotiated']['CurrencyCode'];
                    }
                } elseif (isset($price['error'])) {
                    $content .= json_encode($price['error']);
                }
                $this->getResponse()
                    ->setContent($content);
                return;
            }
        } else {
            $this->getResponse()
                ->setContent('Error (price 1001)');
            return;
        }
    }
}
