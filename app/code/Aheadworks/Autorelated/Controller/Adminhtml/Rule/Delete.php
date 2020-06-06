<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Delete
 *
 * @package Aheadworks\Autorelated\Controller\Adminhtml\Rule
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Autorelated::rule';

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $redirectBack = $this->getRequest()->getParam('back');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $this->ruleRepository->deleteById($id);
                $this->messageManager->addSuccess(__('Rule was successfully deleted'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
                if ($redirectBack !== 'listing') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
