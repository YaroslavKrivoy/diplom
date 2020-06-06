<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\View;

use Magento\Framework\App\Action\Context;
use Aheadworks\Autorelated\Api\StatisticManagerInterface;

/**
 * Class Render
 * @package Aheadworks\Autorelated\Controller\Block
 */
class Process extends \Magento\Framework\App\Action\Action
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
     * Returns block content depends on ajax request
     *
     * @return \Magento\Framework\Controller\Result\Redirect|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        $blocksData = $this->getBlocksDataFromRequest();
        $ruleIds  = $this->getRuleIdsFromBlocksData($blocksData);
        $processedRules = $this->processViewsForRules($ruleIds);
        $this->getResponse()->appendBody(json_encode($processedRules));
    }

    /**
     * Retrieve blocks data from ajax request
     *
     * @return mixed
     */
    private function getBlocksDataFromRequest()
    {
        return $this->getRequest()->getParam('blocks');
    }

    /**
     * Retrieve rule ids from blocks data
     *
     * @param mixed $blocksData
     * @return array
     */
    private function getRuleIdsFromBlocksData($blocksData)
    {
        $ruleIds = [];
        $blocksDataArray = $this->getDecodedBlocksData($blocksData);
        if ($blocksDataArray && is_array($blocksDataArray)) {
            foreach ($blocksDataArray as $blockData) {
                $ruleId = $this->getBlockRuleId($blockData);
                if (!empty($ruleId)) {
                    $ruleIds[] = $ruleId;
                }
            }
        }
        return array_unique($ruleIds);
    }

    /**
     * Decode blocks data
     *
     * @param mixed $blocksData
     * @return array
     */
    private function getDecodedBlocksData($blocksData)
    {
        return json_decode($blocksData);
    }

    /**
     * Retrieve rule id from exact block data
     *
     * @param mixed $blockData
     * @return int
     */
    private function getBlockRuleId($blockData)
    {
        $ruleId = $blockData;
        return $ruleId;
    }

    /**
     * Process view action for specified rules
     *
     * @param array $ruleIds
     * @return array
     */
    private function processViewsForRules($ruleIds)
    {
        $processedRules = [];
        foreach ($ruleIds as $id) {
            if ($this->statisticManager->updateRuleViews($id)) {
                $processedRules[] = $id;
            }
        }
        return $processedRules;
    }
}
