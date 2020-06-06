<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Plugin;

class AddToolbarButtons
{
    protected $_context;
    protected $labelItems;
    protected $shipment;
    protected $creditmemo;

    /**
     * AddToolbarButtons constructor.
     * @param \Infomodus\Upslabel\Model\Items $labelItems
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     */
    public function __construct(
        \Infomodus\Upslabel\Model\Items $labelItems,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magento\Sales\Model\Order\Creditmemo $creditmemo
    )
    {
        $this->labelItems = $labelItems;
        $this->shipment = $shipment;
        $this->creditmemo = $creditmemo;
    }

    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    )
    {
        $this->_context = $context;
        if ($context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            if ($context->getToolbar()->getChildBlock('create_ups_labels_for_order') === false) {
                $context->getToolbar()->addChild(
                    'create_ups_labels_for_order',
                    'Magento\Backend\Block\Widget\Button\SplitButton',
                    [
                        'label' => __('Configure UPS label'),
                        'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                        'button_class' => 'widget-button-save',
                        'options' => $this->_getOrderButtonOptions()
                    ]
                );
            }

            if ($context->getToolbar()->getChildBlock('show_ups_labels_for_order') === false) {
                $labels = $this->labelItems->getCollection()
                    ->addFieldToFilter('order_id', $this->_context->getRequest()->getParam('order_id'));
                if (count($labels) > 0) {
                    $buttonList->add(
                        'show_ups_labels_for_order',
                        [
                            'label' => __('Show UPS label(s)'),
                            'class' => 'primary',
                            'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/show',
                                    ['order_id' => $this->_context->getRequest()->getParam('order_id'),
                                        'redirect_path' => 'order']) . '")',
                        ]
                    );
                }
            }
        }

        if ($context instanceof \Magento\Shipping\Block\Adminhtml\View) {
            if ($context->getToolbar()->getChildBlock('create_ups_labels') === false) {
                $shipment = $this->shipment->load($this->_context->getRequest()->getParam('shipment_id'));
                $context->getToolbar()->addChild(
                    'create_ups_labels',
                    'Magento\Backend\Block\Widget\Button\SplitButton',
                    [
                        'label' => __('Create UPS label'),
                        'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                        'button_class' => 'widget-button-save',
                        'options' => $this->_getOrderButtonOptions(false, $shipment->getOrderId(), 'shipment')
                    ]
                );
            }

            if ($context->getToolbar()->getChildBlock('show_ups_labels') === false) {
                $labels = $this->labelItems->getCollection()
                    ->addFieldToFilter('order_id', $shipment->getOrderId())
                    ->addFieldToFilter('shipment_id', $this->_context->getRequest()->getParam('shipment_id'));
                if (count($labels) > 0) {
                    $buttonList->add(
                        'show_ups_labels',
                        [
                            'label' => __('Show UPS label(s)'),
                            'class' => 'primary',
                            'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/show',
                                    ['order_id' => $shipment->getOrderId(),
                                        'shipment_id' => $this->_context->getRequest()->getParam('shipment_id'),
                                        'type' => 'shipment', 'redirect_path' => 'shipment']) . '")',
                        ]
                    );
                }
            }
        }

        if ($context instanceof \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View) {
            if ($context->getToolbar()->getChildBlock('create_ups_labels_for_creditmemo') === false) {
                $shipment = $this->creditmemo->load($this->_context->getRequest()->getParam('creditmemo_id'));
                $buttonList->add(
                    'create_ups_labels_for_creditmemo',
                    [
                        'label' => __('Create UPS label'),
                        'class' => 'primary',
                        'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/edit',
                                ['order_id' => $shipment->getOrderId(),
                                    'shipment_id' => $this->_context->getRequest()->getParam('creditmemo_id'),
                                    'direction' => 'refund', 'redirect_path' => 'creditmemo']) . '")',
                    ]
                );
            }

            if ($context->getToolbar()->getChildBlock('show_ups_labels_for_creditmemo') === false) {
                $labels = $this->labelItems->getCollection()
                    ->addFieldToFilter('order_id', $shipment->getOrderId())
                    ->addFieldToFilter('shipment_id', $this->_context->getRequest()->getParam('shipment_id'));
                if (count($labels) > 0) {
                    $buttonList->add(
                        'show_ups_labels_for_creditmemo',
                        [
                            'label' => __('Show UPS label(s)'),
                            'class' => 'primary',
                            'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/show', ['order_id' => $shipment->getOrderId(), 'shipment_id' => $this->_context->getRequest()->getParam('creditmemo_id'), 'type' => 'creditmemo', 'redirect_path' => 'creditmemo']) . '")',
                        ]
                    );
                }
            }
        }

        return [$context, $buttonList];
    }

    protected function _getOrderButtonOptions($isReturn = true, $orderId = null, $redirect_path = 'order')
    {
        $options = [];
        $options[] = [
            'id' => 'create_ups_label_direct',
            'label' => __('Shipping UPS label'),
            'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/edit', [
                    'order_id' => $this->_context->getRequest()->getParam('order_id', $orderId),
                    'shipment_id' => $this->_context->getRequest()->getParam('shipment_id', null),
                    'direction' => 'shipment',
                    'redirect_path' => $redirect_path
                ]) . '")',
            'default' => true,
        ];

        if ($isReturn === true) {
            $options[] = [
                'id' => 'create_ups_label_return',
                'label' => __('RMA(return) UPS label'),
                'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/edit', [
                        'order_id' => $this->_context->getRequest()->getParam('order_id', $orderId),
                        'direction' => 'refund',
                        'redirect_path' => $redirect_path
                    ]) . '")',
            ];
        }

        $options[] = [
            'id' => 'create_ups_label_invert',
            'label' => __('Invert UPS label'),
            'onclick' => 'setLocation("' . $this->_context->getUrl('infomodus_upslabel/items/edit', [
                    'order_id' => $this->_context->getRequest()->getParam('order_id', $orderId),
                    'shipment_id' => $this->_context->getRequest()->getParam('shipment_id', null),
                    'direction' => 'invert',
                    'redirect_path' => $redirect_path
                ]) . '")',
        ];

        return $options;
    }
}
