<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Zend\Uri\UriFactory;
use Aheadworks\Autorelated\Api\StatisticManagerInterface;

/**
 * Class CheckRelatedProduct
 *
 * @package Aheadworks\Autorelated\Observer
 */
class CheckRelatedProduct implements ObserverInterface
{

    /**
     * @var StatisticManagerInterface
     */
    private $statisticManager;

    /**
     * @param StatisticManagerInterface $statisticManager
     */
    public function __construct(
        StatisticManagerInterface $statisticManager
    ) {
        $this->statisticManager = $statisticManager;
    }

    /**
     * Check clicking on the product or add to cart link from ARP block
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $ruleId = $this->getRuleId($request);

        if (!$ruleId) {
            return $this;
        }

        $this->statisticManager->updateRuleClicks($ruleId);

        return $this;
    }

    /**
     * Get ARP rule id
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return int|null
     */
    private function getRuleId($request)
    {
        $ruleId = null;
        if ($request->getParam('awarp_rule')) {
            $ruleId = (int)$request->getParam('awarp_rule');
        } elseif ($actionUrl = $request->getParam('action_url')) {
            // In case action is interrupted by AW ACP
            // try to extract rule id from 'action_url' parameter
            $path = trim(UriFactory::factory($actionUrl)->getPath(), '/');
            $params = explode('/', $path);
            for ($i = 0, $length = sizeof($params); $i < $length; $i++) {
                if ($params[$i] == 'awarp_rule' && isset($params[$i + 1])) {
                    $ruleId = urldecode($params[$i + 1]);
                    break;
                }
            }
        }
        return $ruleId;
    }
}
