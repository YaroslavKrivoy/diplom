<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items;

class Search extends \Magento\Sales\Block\Adminhtml\Order\Create\Search
{
    /**
     * Get buttons html
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonHtml = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'label' => __('Add Selected Product(s) to Order'),
                    'class' => 'action-add action-secondary',
                    'id'    => 'ordereditor-apply-add-products'
                ]
            )->toHtml();

        $cancelButtonHtml = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(
                [
                    'label' => __('Cancel'),
                    'class' => 'action-cancel action-secondary',
                    'id'    => 'ordereditor-cancel-add-products'
                ]
            )->toHtml();

        return $cancelButtonHtml . $addButtonHtml;
    }
}
