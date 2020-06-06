<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit;

use Magento\Framework\View\Element\Template;

class Wrapper extends Template
{
    /**
     * @return string
     */
    public function getJsonParamsItems()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/items'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonParamsAddress()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/address'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonParamsShipping()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/shipping'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonParamsPayment()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/payment'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonParamsAccount()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/account'),
            'renderGridUrl' => $this->getUrl('ordereditor/edit_account_widget/chooser'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getJsonParamsInfo()
    {
        $data = [
            'loadFormUrl' => $this->getUrl('ordereditor/form/load'),
            'updateUrl' => $this->getUrl('ordereditor/edit/info'),
            'isAllowed' => true
        ];

        return json_encode($data);
    }
}
