<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\Adminhtml\Rule\Widget;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Aheadworks\Autorelated\Block\Adminhtml\Widget\Rule\Chooser as RuleWidgetChooser;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * Chooser Source action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $this->layoutFactory->create();
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $layout->createBlock(
            RuleWidgetChooser::class,
            '',
            ['data' => ['id' => $uniqId]]
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($pagesGrid->toHtml());
        return $resultRaw;
    }
}
