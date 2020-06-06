<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

use Magento\Framework\App\RequestInterface;

class AbstractPlugin
{
    const BLOCK_INFO             = 'info';
    const BLOCK_ACCOUNT          = 'account';
    const BLOCK_BILLING_ADDRESS  = 'billing_address';
    const BLOCK_SHIPPING_ADDRESS = 'shipping_address';
    const BLOCK_ORDER_ITEMS      = 'order_items';
    const BLOCK_SHIPPING_METHOD  = 'shipping_method';
    const BLOCK_PAYMENT_METHOD   = 'payment_method';

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * BeforeCollectTotalsPlugin constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    protected function isOrderEdit()
    {
        $post = $this->request->getPost();

        if (!empty($post['block_id']) && in_array(
                $post['block_id'],
                array(
                    self::BLOCK_INFO,
                    self::BLOCK_ACCOUNT,
                    self::BLOCK_BILLING_ADDRESS,
                    self::BLOCK_SHIPPING_ADDRESS,
                    self::BLOCK_SHIPPING_METHOD,
                    self::BLOCK_PAYMENT_METHOD,
                    self::BLOCK_ORDER_ITEMS,
                )
            )) {
            return true;
        }
        if (!empty($post['shipping_method'])) {
            return true;
        }

        return false;
    }
}