<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Delete
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Button
 */
class Delete implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->context = $context;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($id = $this->getId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to do this?'),
                    $this->getUrl('*/*/delete', ['id' => $id])
                ),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Return rule ID
     *
     * @return int|null
     */
    public function getId()
    {
        $ruleId = $this->context->getRequest()->getParam('id');

        if ($ruleId && $this->ruleRepository->get($ruleId)) {
            return $this->ruleRepository->get($ruleId)->getId();
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
