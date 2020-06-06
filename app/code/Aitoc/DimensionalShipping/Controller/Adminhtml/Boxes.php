<?php

namespace Aitoc\DimensionalShipping\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;

abstract class Boxes extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $entityTypeId;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $ulrGenerator;
    protected $boxFactory;
    protected $boxRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product\Url $ulrGenerator,
        \Aitoc\DimensionalShipping\Model\BoxFactory $boxFactory,
        \Aitoc\DimensionalShipping\Model\BoxRepository $boxRepository
    ) {
        parent::__construct($context);
        $this->coreRegistry      = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->ulrGenerator      = $ulrGenerator;
        $this->boxFactory        = $boxFactory;
        $this->boxRepository     = $boxRepository;
    }

    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
        }
        $resultPage->setActiveMenu('Aitoc_DimensionalShipping::boxes');
        $resultPage->getConfig()->getTitle()->prepend(__('Boxes'));

        return $resultPage;
    }
}
