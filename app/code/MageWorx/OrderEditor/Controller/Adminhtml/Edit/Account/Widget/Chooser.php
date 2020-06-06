<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit\Account\Widget;

use Magento\Backend\App\Action;

class Chooser extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Chooser Source action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $uniqueId = $this->getRequest()->getParam('unique_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);

        $layout = $this->layoutFactory->create();
        $customersGrid = $layout->createBlock(
            \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Account\Widget\Chooser::class,
            '',
            [
                'data' => [
                    'id' => $uniqueId,
                    'use_massaction' => $massAction,
                ]
            ]
        );

        $html = $customersGrid->toHtml();

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }
}
