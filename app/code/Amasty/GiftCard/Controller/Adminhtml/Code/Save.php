<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Amasty\GiftCard\Api\Data\CodeSetInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Amasty\GiftCard\Model\CodeSetFactory;
use Amasty\GiftCard\Model\CodeFactory;
use Amasty\GiftCard\Model\ResourceModel\CodeSet;
use Amasty\GiftCard\Model\ResourceModel\Code;
use Magento\Ui\Component\MassAction\Filter;
use Amasty\GiftCard\Model\ResourceModel\CodeSet\Collection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller;
use Magento\SalesRule\Model\RuleFactory;

class Save extends \Amasty\GiftCard\Controller\Adminhtml\Code
{
    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        LoggerInterface $logInterface,
        CodeSetFactory $codeSetFactory,
        CodeFactory $codeFactory,
        CodeSet $codeSetResource,
        Code $codeResource,
        Filter $filter,
        Collection $codeSetCollection,
        RuleFactory $ruleFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        parent::__construct(
            $context,
            $resultPageFactory,
            $coreRegistry,
            $logInterface,
            $codeSetFactory,
            $codeFactory,
            $codeSetResource,
            $codeResource,
            $filter,
            $codeSetCollection
        );
    }

    /**
     * @return ResponseInterface|Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();

            try {
                $model = $this->codeSetFactory->create();

                $conditions = $this->getRequest()->getParam('rule');
                if ($conditions) {
                    /** @var \Magento\SalesRule\Model\Rule $rule */
                    $rule = $this->ruleFactory->create();
                    $rule->loadPost($conditions)
                        ->beforeSave();
                    $data[CodeSetInterface::CONDITIONS_SERIALIZED] = $rule->getData(CodeSetInterface::CONDITIONS_SERIALIZED);
                }

                $id = $this->getRequest()->getParam('code_set_id');
                if ($id) {
                    $this->codeSetResource->load($model, $id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong Code Pool is specified.'));
                    }
                }

                $field = 'csv';
                if (!empty($this->getRequest()->getFiles($field)['tmp_name'])) {
                    try {
                        $info = pathinfo($this->getRequest()->getFiles($field)['tmp_name']);
                        $data['csv_info'] = $info;
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                }

                $model->setData($data);
                $this->codeSetResource->save($model);

                $this->messageManager->addSuccessMessage(__('Record has been successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('code_set_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/index');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the Code Pool data. Please review the error log.')
                );
                $this->logInterface->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('code_set_id')]);
                return;
            }
        }
    }
}
