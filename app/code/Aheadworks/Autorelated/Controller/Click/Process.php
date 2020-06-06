<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\Click;

use Magento\Framework\App\Action\Context;
use Aheadworks\Autorelated\Api\StatisticManagerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Process
 * @package Aheadworks\Autorelated\Controller\Click
 */
class Process extends Action
{
    /**
     * @var StatisticManagerInterface
     */
    private $statisticManager;

    /**
     * @param Context $context
     * @param StatisticManagerInterface $statisticManager
     */
    public function __construct(
        Context $context,
        StatisticManagerInterface $statisticManager
    ) {
        parent::__construct($context);
        $this->statisticManager = $statisticManager;
    }

    /**
     * Update click statistics for block
     *
     * @return \Magento\Framework\Controller\Result\Redirect|null
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        try {
            if ($ruleId = (int)$this->getRequest()->getParam('awarp_rule')) {
                $this->statisticManager->updateRuleClicks($ruleId);
            }
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }
}
