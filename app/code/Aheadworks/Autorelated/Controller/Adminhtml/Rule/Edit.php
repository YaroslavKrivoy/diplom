<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\Adminhtml\Rule;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action\Context;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 *
 * @package Aheadworks\Autorelated\Controller\Adminhtml\Rule
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Autorelated::rule';

    /**
     * Edit rule
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->ruleRepository->get($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addException($exception, __('Something went wrong while editing the rule'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Autorelated::rule')
            ->getConfig()->getTitle()->prepend(
                $id ? __('Edit Rule') : __('New Rule')
            );

        return $resultPage;
    }
}
