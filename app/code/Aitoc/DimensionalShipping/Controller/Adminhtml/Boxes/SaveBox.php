<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

use Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;
use Aitoc\DimensionalShipping\Helper\Data;
use Magento\Framework\View\Result\PageFactory;

class SaveBox extends Boxes
{
    protected $helper;

    protected $boxRepository;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context            $context
     * @param \Magento\Framework\Registry                    $coreRegistry
     * @param PageFactory                                    $resultPageFactory
     * @param \Magento\Catalog\Model\Product\Url             $ulrGenerator
     * @param \Aitoc\DimensionalShipping\Model\BoxFactory    $boxFactory
     * @param \Aitoc\DimensionalShipping\Model\BoxRepository $boxRepository
     * @param Data                                           $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product\Url $ulrGenerator,
        \Aitoc\DimensionalShipping\Model\BoxFactory $boxFactory,
        \Aitoc\DimensionalShipping\Model\BoxRepository $boxRepository,
        Data $helper
    ) {
        $this->helper        = $helper;
        $this->boxRepository = $boxRepository;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $ulrGenerator, $boxFactory, $boxRepository);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost() && ($postData = $this->getRequest()->getPostValue())) {
            try {
                $boxModel = $this->boxRepository->create();
                $boxModel->setData($postData);
                if ($postData['item_id'] == '') {
                    $boxModel->setUnit($this->helper->getGeneralConfig('unit'));
                    $boxModel->setItemId(null);
                }
                $this->boxRepository->save($boxModel);
                $this->messageManager->addSuccess(__('The box saved successfully.'));
                $redirectResult->setPath('aitdimensionalshipping/*/');

                return $redirectResult;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPostData($postData);
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving. Please review the error log.')
                );
                $this->_getSession()->setPostData($postData);
            }
            $redirectResult->setUrl($this->_redirect->getRedirectUrl($this->getUrl('*')));

            return $redirectResult;
        }
        $redirectResult->setPath('aitdimensionalshipping/*/');

        return $redirectResult;
    }
}
