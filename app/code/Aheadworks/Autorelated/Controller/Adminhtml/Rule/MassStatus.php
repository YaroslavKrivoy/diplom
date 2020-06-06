<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Controller\Adminhtml\Rule;

use Aheadworks\Autorelated\Model\ResourceModel\Rule\Collection;
use Magento\Framework\Controller\ResultFactory;
use Aheadworks\Autorelated\Model\RuleStatusManager;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Autorelated\Model\ResourceModel\Rule\CollectionFactory;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;

/**
 * Class MassStatus
 *
 * @package Aheadworks\Autorelated\Controller\Adminhtml\Rule
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @var RuleStatusManager
     */
    private $ruleStatusManager;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleStatusManager $ruleStatusManager
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RuleRepositoryInterface $ruleRepository,
        RuleStatusManager $ruleStatusManager
    ) {
        $this->ruleStatusManager = $ruleStatusManager;
        parent::__construct($context, $filter, $collectionFactory, $ruleRepository);
    }

    /**
     * @inheritdoc
     */
    protected function massAction(Collection $collection)
    {
        $status = (bool) $this->getRequest()->getParam('status');
        $updatedRecords = 0;

        foreach ($collection->getAllIds() as $ruleId) {
            if (!$this->ruleStatusManager->isRuleStatusLockedByWvtavFunctionality($ruleId)) {
                $rule = $this->ruleRepository->get($ruleId);
                $rule->setStatus($status);
                $this->ruleRepository->save($rule);
                $updatedRecords++;
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
