<?php
namespace MageArray\StorePickup\Block\Adminhtml\Store;

/**
 * Class Grid
 * @package MageArray\StorePickup\Block\Adminhtml\Store
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \MageArray\StorePickup\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \MageArray\StorePickup\Model\StoreFactory $storeFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Helper\Data $backendHelper,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_storeFactory = $storeFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_resource = $resource;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('storeGrid');
        $this->setDefaultSort('storepickup_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_storeFactory->create()->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'storepickup_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'storepickup_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '10px',
            ]
        );
        $this->addColumn(
            'store_name',
            [
                'header' => __('Name'),
                'index' => 'store_name',
            ]
        );
        $this->addColumn(
            'address',
            [
                'header' => __('Address'),
                'index' => 'address',
            ]
        );

        $this->addColumn(
            'city',
            [
                'header' => __('City'),
                'index' => 'city',
            ]
        );

        $this->addColumn(
            'zipcode',
            [
                'header' => __('Zipcode'),
                'index' => 'zipcode',
            ]
        );

        $this->addColumn(
            'country',
            [
                'header' => __('Country'),
                'index' => 'country',
            ]
        );

        $this->addColumn(
            'phone_number',
            [
                'header' => __('Phone Number'),
                'index' => 'phone_number',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'status',
                'type' => 'action',
                'getter' => 'getId',
                'class' => 'xxx',
                'width' => '20px',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'field' => 'storepickup_id',
                        'url' => [
                            'base' => '*/*/edit',
                        ]

                    ]
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['storepickup_id' => $row->getId()]);
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('storepickup_id');
        $this->getMassactionBlock()->setFormFieldName('store');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Remove Store'),
            'url' => $this->getUrl('*/*/massDelete', ['' => '']),
            'confirm' => __('Are you sure?')
        ]);

        return $this;
    }
}
