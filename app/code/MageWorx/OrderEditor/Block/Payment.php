<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\ConfigProviderInterface;
use MageWorx\OrderEditor\Model\Ui\ConfigProvider;

/**
 * Class Payment
 *
 */
class Payment extends Template
{
    /**
     * @var ConfigProviderInterface
     */
    private $config;

    /**
     * Payment constructor.
     *
     * @param ConfigProviderInterface $config
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ConfigProviderInterface $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return ConfigProvider::CODE;
    }

    /**
     * Payment config
     *
     * @return string
     */
    public function getPaymentConfig()
    {
        $paymentConfig  = $this->config->getConfig()['payment'];
        $config         = $paymentConfig[$this->getCode()];
        $config['code'] = $this->getCode();

        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }
}