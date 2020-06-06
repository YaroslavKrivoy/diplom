<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Block\Adminhtml\Boxes\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 *
 * @package Aitoc\MultiLocationInventory\Block\Adminhtml\Supplier\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data  = [];
        $boxId = $this->getBoxId();
        if ($boxId) {
            $data = [
                'label'      => __('Delete Box'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' .
                    __('Are you sure you want to do this?') .
                    '\', \'' .
                    $this->urlBuilder->getUrl('*/*/delete', ['id' => $boxId]) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
