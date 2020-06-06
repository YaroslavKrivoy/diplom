<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Widget\Rule;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as BlockWidgetChooser;
use Aheadworks\Autorelated\Model\RuleFactory;
use Aheadworks\Autorelated\Model\Source\Status;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Source\Type;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;

/**
 * Class Chooser
 * @package Aheadworks\Autorelated\Block\Adminhtml\Widget\Rule
 */
class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**#@+
     * Constants for grid columns IDs
     */
    const CHOOSER_ID_COLUMN_ID = 'chooser_id';
    const CHOOSER_NAME_COLUMN_ID = 'chooser_name';
    const CHOOSER_STATUS_COLUMN_ID = 'chooser_status';
    /**#@-*/

    /**
     * @var Status
     */
    private $statusSource;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param Status $statusSource
     * @param RuleFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        Status $statusSource,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->statusSource = $statusSource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Rule construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter([self::CHOOSER_STATUS_COLUMN_ID => Status::STATUS_ENABLED]);
    }

    /**
     * Prepare columns for ARP rules grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            self::CHOOSER_ID_COLUMN_ID,
            [
                'header' => __('ID'),
                'align' => 'right',
                'index' => 'id',
                'width' => 50
            ]
        );
        $this->addColumn(
            self::CHOOSER_NAME_COLUMN_ID,
            [
                'header' => __('Name'),
                'align' => 'left',
                'index' => 'title'
            ]
        );
        $this->addColumn(
            self::CHOOSER_STATUS_COLUMN_ID,
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->statusSource->getOptionArray()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare ARP rules collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->ruleFactory->create()->getCollection()
            ->addFieldToFilter(RuleInterface::TYPE, ['eq' => Type::CUSTOM_BLOCK_TYPE])
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('autorelated_admin/rule_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()
            ->createBlock(BlockWidgetChooser::class)
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $rule = $this->ruleRepository->get($element->getValue());
            $chooser->setLabel($this->escapeHtml($rule->getTitle()));
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML; '
            . $chooserJsObject . '.setElementValue(blockId); '
            . $chooserJsObject . '.setElementLabel(blockTitle); '
            . $chooserJsObject . '.close();
            }';
        return $js;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('autorelated_admin/rule_widget/chooser', ['_current' => true]);
    }
}
