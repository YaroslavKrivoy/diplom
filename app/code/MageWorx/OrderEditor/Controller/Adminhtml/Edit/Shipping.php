<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;

class Shipping extends AbstractAction
{
    /**
     * @return void
     */
    protected function update()
    {
        $this->updateShippingMethod();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateShippingMethod()
    {
        $params = $this->prepareParams();
        $this->shipping->initParams($params);

        $this->shipping->updateShippingMethod();
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function prepareParams()
    {
        $params = [
            'shipping_method',
            'order_id',
            'price_excl_tax',
            'price_incl_tax',
            'tax_percent',
            'description',
            'discount_amount'
        ];

        foreach ($params as $param) {
            $val = $this->getRequest()->getParam($param, null);
            if ($val == null) {
                throw new \Exception('Empty param ' . $param);
            }
            $params[$param] = $val;
        }

        return $params;
    }

    /**
     * @return bool
     */
    protected function needUpdateShippingInfo()
    {
        return false;
    }
}
