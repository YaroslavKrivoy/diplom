<?php

namespace Aitoc\DimensionalShipping\Helper;

use Aitoc\DimensionalShipping\Model\BoxRepository;
use Aitoc\DimensionalShipping\Model\Convertor\ConvertorFactory;
use Aitoc\DimensionalShipping\Model\OrderBoxRepository;
use Aitoc\DimensionalShipping\Model\OrderItemBoxFactory;
use Aitoc\DimensionalShipping\Model\OrderItemBoxRepository;
use Aitoc\DimensionalShipping\Model\ProductOptionsRepository;
use Aitoc\DimensionalShipping\Model\ResourceModel\Box\CollectionFactory as BoxCollectionFactory;
use Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox\CollectionFactory as OrderBoxCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 *
 * @package Aitoc\DimensionalShipping\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    //DS product settings fields
    const XML_PATH_CONFIG = 'DimensionalShipping/general/';
    //global fields
    protected $fields = ['width', 'height', 'length', 'special_box', 'select_box', 'pack_separately'];
    //data for render DS field in admin: product page
    protected $globalFields = ['button_text'];
    protected $containerData = [];
    protected $productImagesFolderPath = 'catalog/product';
    protected $scopeConfig;
    protected $boxCollectionFactory;
    protected $orderItemBoxModelFactory;
    protected $boxRepository;
    protected $boxCollection;
    protected $dimensionalProductOptionsRepository;
    protected $dimensionalOrderItemBoxesRepository;
    protected $orderBoxesRepository;
    protected $orderBoxCollectionFactory;
    protected $storeManager;
    protected $convertorFactory;

    /**
     * Data constructor.
     *
     * @param ScopeConfigInterface      $scopeConfig
     * @param BoxCollectionFactory      $boxCollectionFactory
     * @param OrderItemBoxFactory       $orderItemBoxModelFactory
     * @param BoxRepository             $boxRepository
     * @param ProductOptionsRepository  $dimensionalProductOptionsRepository
     * @param OrderItemBoxRepository    $dimensionalOrderItemBoxesRepository
     * @param OrderBoxCollectionFactory $orderBoxCollectionFactory
     * @param OrderBoxRepository        $orderBoxesRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BoxCollectionFactory $boxCollectionFactory,
        OrderItemBoxFactory $orderItemBoxModelFactory,
        BoxRepository $boxRepository,
        ProductOptionsRepository $dimensionalProductOptionsRepository,
        OrderItemBoxRepository $dimensionalOrderItemBoxesRepository,
        OrderBoxCollectionFactory $orderBoxCollectionFactory,
        OrderBoxRepository $orderBoxesRepository,
        StoreManagerInterface $storeManager,
        ConvertorFactory $convertorFactory
    ) {
        $this->orderItemBoxModelFactory            = $orderItemBoxModelFactory;
        $this->scopeConfig                         = $scopeConfig;
        $this->boxCollectionFactory                = $boxCollectionFactory;
        $this->boxRepository                       = $boxRepository;
        $this->dimensionalProductOptionsRepository = $dimensionalProductOptionsRepository;
        $this->dimensionalOrderItemBoxesRepository = $dimensionalOrderItemBoxesRepository;
        $this->orderBoxCollectionFactory           = $orderBoxCollectionFactory;
        $this->orderBoxesRepository                = $orderBoxesRepository;
        $this->storeManager                        = $storeManager;
        $this->convertorFactory                    = $convertorFactory;
        $this->defineContainerData();
    }

    public function defineContainerData()
    {
        $this->containerData = [
            'container_length'          => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 0,
                            'sortOrder'     => 0,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'length' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'input',
                                    'visible'       => 1,
                                    'required'      => 0,
                                    'notice'        => 'The length of the product for the carton selection algorithm',
                                    'default'       => '',
                                    'label'         => 'Product Length',
                                    'code'          => 'product_length',
                                    'source'        => '',
                                    'globalScope'   => '',
                                    'sortOrder'     => 3,
                                    'componentType' => 'field',
                                    'addafter'      => $this->getGeneralConfig('unit')
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'container_width'           => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 0,
                            'sortOrder'     => 1,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'width' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'input',
                                    'visible'       => 1,
                                    'required'      => 0,
                                    'notice'        => 'The width of the product for the carton selection algorithm',
                                    'default'       => '',
                                    'label'         => 'Product Width',
                                    'code'          => 'product_width',
                                    'source'        => '',
                                    'globalScope'   => '',
                                    'sortOrder'     => 0,
                                    'componentType' => 'field',
                                    'addafter'      => $this->getGeneralConfig('unit')
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'container_height'          => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 0,
                            'sortOrder'     => 2,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'height' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'input',
                                    'visible'       => 1,
                                    'required'      => 0,
                                    'notice'        => 'The height of the product for the carton selection algorithm',
                                    'default'       => '',
                                    'label'         => 'Product Height',
                                    'code'          => 'product_height',
                                    'source'        => '',
                                    'globalScope'   => '',
                                    'sortOrder'     => 0,
                                    'componentType' => 'field',
                                    'addafter'      => $this->getGeneralConfig('unit')
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'container_special_box'     => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 0,
                            'sortOrder'     => 4,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'special_box' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'select',
                                    'visible'       => 1,
                                    'required'      => 0,
                                    'notice'        => 'Special Box for this product:',
                                    'default'       => '',
                                    'label'         => 'Special Box for this product:',
                                    'code'          => 'special_box',
                                    'source'        => '',
                                    'globalScope'   => '',
                                    'sortOrder'     => 0,
                                    'componentType' => 'field',
                                    'component'     => 'Aitoc_DimensionalShipping/js/form/element/options',
                                    'options'       => [
                                        ['label' => 'Disable', 'value' => 0],
                                        ['label' => 'Enable', 'value' => 1]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'container_select_box'      => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 1,
                            'sortOrder'     => 5,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'select_box' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'select',
                                    'required'      => 0,
                                    'notice'        => 'Choose box for this product',
                                    'label'         => 'Box',
                                    'code'          => 'special_box',
                                    'source'        => '',
                                    'default'       => '',
                                    'visible'       => 0,
                                    'globalScope'   => '',
                                    'sortOrder'     => 0,
                                    'componentType' => 'field',
                                    'options'       => [],
                                    'required'      => 1,
                                    'validation'    => [
                                        'required-entry' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'container_pack_separately' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'   => 'container',
                            'componentType' => 'container',
                            'breakLine'     => '',
                            'label'         => 'Areas settings',
                            'required'      => 0,
                            'sortOrder'     => 6,
                            'component'     => 'Magento_Ui/js/form/components/group',
                            'dataScope'     => ''
                        ]
                    ]
                ],
                'children'  => [
                    'pack_separately' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType'      => 'int',
                                    'formElement'   => 'select',
                                    'visible'       => 1,
                                    'required'      => 0,
                                    'notice'        => 'Special packing rules for this product: ',
                                    'default'       => '',
                                    'label'         => 'Special packing rules for this product: ',
                                    'code'          => 'pack_separately',
                                    'source'        => '',
                                    'globalScope'   => '',
                                    'sortOrder'     => 0,
                                    'componentType' => 'field',
                                    'options'       => [
                                        ['label' => 'No', 'value' => 0],
                                        ['label' => 'Separate box for each item', 'value' => 1],
                                        ['label' => 'Separate box for several items of this product', 'value' => 2]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG . $code, $storeId);
    }

    /**
     * @param      $field
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getGlobalFields()
    {
        return $this->globalFields;
    }

    /**
     * @return array
     */
    public function getContainerData()
    {
        return $this->containerData;
    }

    /**
     * @param null $option
     * @param      $orderId
     *
     * @return array|\Magento\Framework\DataObject[]\
     */
    public function getBoxListForOrder($orderId)
    {
        $orderBoxCollection = $this->orderBoxCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)->getItems();
        $items              = $orderBoxCollection;
        foreach ($orderBoxCollection as $item) {
            $optionsArray[] = ['label' => 'Select Box', 'value' => ''];
            foreach ($items as $rowItem) {
                $rowItem->getData();
                $optionsArray[] = ['label' => $rowItem->getName(), 'value' => $rowItem->getItemId()];
            }
        }

        return $items;
    }

    /**
     * @param null $option
     *
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getBoxList($option = null)
    {
        $collection     = $this->boxCollectionFactory->create();
        $items          = $collection->getItems();
        $optionsArray[] = ['label' => 'Select Box', 'value' => ''];
        foreach ($items as $rowItem) {
            $rowItem->getData();
            $optionsArray[] = ['label' => $rowItem->getName(), 'value' => $rowItem->getItemId()];
        }
        if ($option == 'array') {
            foreach ($items as $rowItem) {
                $boxes[] = $rowItem->getData();
            }

            return $boxes;
        }
        if ($option == 'items') {
            return $items;
        }

        return $optionsArray;
    }

    /**
     * @param $orderItemId
     *
     * @return \Aitoc\DimensionalShipping\Model\OrderItemBox
     */
    public function getBoxByOrderItemId($orderItemId)
    {
        $orderItem = $this->dimensionalOrderItemBoxesRepository->getByOrderItemId($orderItemId);

        return $orderItem;
    }

    /**
     * @param $boxId
     *
     * @return \Aitoc\DimensionalShipping\Api\Data\BoxInterface|mixed
     */
    public function getBoxById($boxId)
    {
        try {
            $box = $this->boxRepository->get($boxId);

            return $box;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param      $boxId
     * @param      $itemId
     * @param      $orderId
     * @param      $weight
     * @param bool $separate
     */
    public function saveProductsInBox($boxId, $itemId, $orderId, $weight, $sku, $separate = false)
    {
        $orderBoxModel = $this->orderBoxesRepository->create();
        $orderBoxModel->setBoxId($boxId);
        $orderBoxModel->setOrderId($orderId);
        $orderBoxModel->setWeight($weight);
        $orderBoxModel = $this->orderBoxesRepository->save($orderBoxModel);
        $orderItemBox  = $this->dimensionalOrderItemBoxesRepository->create();
        $orderItemBox->setOrderBoxId($orderBoxModel->getItemId());
        $orderItemBox->setOrderItemId($itemId);
        $orderItemBox->setSeparate($separate);
        $orderItemBox->setOrderId($orderId);
        $orderItemBox->setSku($sku);
        $orderItemBox->setNotPacked(0);
        $this->dimensionalOrderItemBoxesRepository->save($orderItemBox);
    }

    /**
     * @param $orderItem
     *
     * @return bool
     */
    public function checkProductsType($orderItem)
    {
        if ($orderItem->getProductType() == 'downloadable'
            || $orderItem->getProductType() == 'virtual'
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $productId
     *
     * @return \Aitoc\DimensionalShipping\Model\ProductOptions|bool
     */
    public function getProductDimensionalOptions($productId)
    {
        $productDSOptions = $this->dimensionalProductOptionsRepository->getByProductId($productId);
        if (!empty($productDSOptions->getData())) {
            return $productDSOptions;
        } else {
            return false;
        }
    }

    /**
     * @param $object
     * @param $type
     *
     * @return mixed
     */
    public function convertUnits($object, $type)
    {
        if ($type == 'box') {
            $boxFields = $this->getBoxModelFields('long');
        } else {
            $boxFields = $this->getProductOptionsModelFields('long');
        }
        foreach ($boxFields as $field) {
            $start     = 'get';
            $field     = $start . str_replace("_", "", $field);
            $convertor = $this->convertorFactory->create(
                [
                    'value' => $object->{$field}(),
                    'unit'  => $this->getGeneralConfig('unit')
                ]
            );
            $result    = $convertor->to(['mm']);
            $field     = str_replace("get", "set", $field);
            $object->{$field}($result['mm']);
        }

        return $object;

    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getBoxModelFields($type)
    {
        if ($type == 'long') {
            $boxFields = $this->boxRepository->getUnitsConfigFieldsLong();
        } else {
            $boxFields = $this->boxRepository->getUnitsConfigFieldsWeight();
        }

        return $boxFields;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getProductOptionsModelFields($type)
    {
        if ($type == 'long') {
            $productOptionsModelFields = $this->dimensionalProductOptionsRepository->getUnitsConfigFieldsLong();
        } else {
            $productOptionsModelFields = $this->dimensionalProductOptionsRepository->getUnitsConfigFieldsWeight();
        }

        return $productOptionsModelFields;
    }
}
