<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Rule\Block\Conditions;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\DataObject;
use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Class AbstractCondition
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCondition extends Generic
{
    /**
     * Form name
     *
     * @var string
     */
    const FORM_NAME = 'aw_autorelated_rule_form';

    /**
     * @var Conditions
     */
    protected $conditions;

    /**
     * @var FieldsetFactory
     */
    protected $rendererFieldsetFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var DataObject
     */
    protected $dataObject;

    /**
     * @var array
     */
    protected $formData;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Conditions $conditions
     * @param FieldsetFactory $rendererFieldsetFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RuleRepositoryInterface $ruleRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObject $dataObject
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Conditions $conditions,
        FieldsetFactory $rendererFieldsetFactory,
        DataPersistorInterface $dataPersistor,
        RuleRepositoryInterface $ruleRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObject $dataObject,
        array $data = []
    ) {
        $this->conditions = $conditions;
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->ruleRepository = $ruleRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $fieldset = $this->addFieldsetToForm($form);
        $this->prepareFieldset($fieldset);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Create form for controls
     *
     * @return \Magento\Framework\Data\Form
     */
    protected function createForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix($this->getFormHtmlIdPrefix());
        return $form;
    }

    /**
     * Add fieldset to specified form
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addFieldsetToForm($form)
    {
        return $form->addFieldset($this->getFieldsetName(), []);
    }

    /**
     * Prepare form fieldset for rendering
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     */
    protected function prepareFieldset($fieldset)
    {
        $conditionData = $this->getConditionData();
        $this->addFieldsToFieldset($fieldset, $conditionData);
    }

    /**
     * Retrieve condition data array from form data
     *
     * @return mixed
     */
    protected function getConditionData()
    {
        $conditionData = null;
        $formData = $this->getFormData();
        if (is_array($formData) && (isset($formData[$this->getFormDataConditionKey()]))) {
            $conditionData = $formData[$this->getFormDataConditionKey()];
        }
        return $conditionData;
    }

    /**
     * Get data for rule blocks
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getFormData()
    {
        if ($this->formData === null) {
            if (!empty($this->dataPersistor->get('aw_autorelated_rule'))) {
                $this->formData = $this->dataPersistor->get('aw_autorelated_rule');
            } elseif ($id = $this->getRequest()->getParam('id')) {
                $rule = $this->ruleRepository->get($id);
                $this->formData = $this->dataObjectProcessor->buildOutputDataArray(
                    $rule,
                    RuleInterface::class
                );
            }
        }

        return $this->formData;
    }

    /**
     * Add necessary fields to form fieldset
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param mixed $conditionData
     */
    abstract protected function addFieldsToFieldset($fieldset, $conditionData);

    /**
     * Retrieve key of condition data in the form data
     *
     * @return string
     */
    abstract protected function getFormDataConditionKey();

    /**
     * Retrieve form html id prefix
     *
     * @return string
     */
    protected function getFormHtmlIdPrefix()
    {
        return 'rule_';
    }

    /**
     * Retrieve fieldset name
     *
     * @return string
     */
    abstract protected function getFieldsetName();
}
