<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Magento\Sales\Model\AbstractModel;
use Magento\Framework\Api\AttributeValueFactory;

class Address extends AbstractModel
{
    /**
     * @var $address \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address
     */
    protected $address;

    /**
     * @var $oldAddress \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address
     */
    protected $oldAddress;

    /**
     * @var \Magento\Customer\Model\Address
     */
    protected $customerAddress;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var string[]
     */
    protected $addressMap = [
        'fax'        => 'Fax',
        'region'     => 'State/Province',
        'postcode'   => 'Zip/Postal Code',
        'lastname'   => 'Last Name',
        'street'     => 'Street Address',
        'city'       => 'City',
        'email'      => 'Email',
        'telephone'  => 'Phone Number',
        'country_id' => 'Country',
        'firstname'  => 'First Name',
        'prefix'     => 'Prefix',
        'middlename' => 'Middle Name/Initial',
        'suffix'     => 'Suffix',
        'company'    => 'Company',
        'vat_id'     => 'VAT Number',
    ];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Customer\Model\Address $customerAddress
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Customer\Model\Address $customerAddress,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->objectManager = $objectManager;
        $this->addressRenderer = $addressRenderer;
        $this->regionFactory = $regionFactory;
        $this->customerAddress = $customerAddress;
    }

    /**
     * @param string[] $addressData
     * @return void
     */
    public function updateAddress($addressData)
    {
        $addressData = $this->prepareRegion($addressData);
        $this->oldAddress = $this->address->getData();

        $this->address->addData($addressData);
        $this->address->save();

        $this->_eventManager->dispatch(
            'admin_sales_order_address_update',
            ['order_id' => $this->address->getParentId()]
        );
    }

    /**
     * @param string[] $addressData
     * @return void
     */
    public function updateCustomerAddress($addressData)
    {
        $customerAddressId = $this->address->getCustomerAddressId();
        $this->customerAddress->load($customerAddressId);
        if ($this->customerAddress->getId()) {
            $this->customerAddress->addData($addressData);
            $this->customerAddress->save();
        }
    }

    /**
     * @param string[] $addressData
     * @return string[]
     */
    protected function prepareRegion($addressData)
    {
        if (isset($addressData['region_id'])
            && !empty($addressData['region_id'])
            && (!isset($addressData['region']) || empty($addressData['region']))
        ) {
            $addressData['region'] = $this->regionFactory->create()
                ->load($addressData['region_id'])
                ->getName();
        }

        return $addressData;
    }

    /**
     * @return null|string
     */
    public function getFormattedAddressString()
    {
        return $this->addressRenderer->format($this->address, 'html');
    }

    /**
     * @param int $addressId
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address
     * @throws \Exception
     */
    public function loadAddress($addressId)
    {
        /**
         * @var $address \Magento\Sales\Api\Data\OrderAddressInterface|\Magento\Sales\Model\Order\Address
         */
        $address = $this->objectManager
            ->create('Magento\Sales\Api\Data\OrderAddressInterface')
            ->load($addressId);

        if (!$address->getId()) {
            throw new \Exception('Can not update order address data');
        }

        return $this->address = $address;
    }
}
