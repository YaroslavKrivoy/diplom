<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

/**
 * Class Index
 *
 * @package Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes
 */
class Index extends \Magento\Backend\App\Action
{

    protected $boxRepository;

    /**
     * Class construct
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context        $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aitoc\DimensionalShipping\Model\BoxRepository $boxRepository
    ) {
        $this->boxRepository     = $boxRepository;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Dimensional Shipping: Boxes List'));

        return $resultPage;
    }
}
