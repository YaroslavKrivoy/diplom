<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use MageWorx\OrderEditor\Model\Address as AddressModel;
use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;

class Address extends AbstractAction
{
    /**
     * @var \MageWorx\OrderEditor\Model\Address
     */
    protected $address;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param Quote $quote
     * @param Order $order
     * @param ShippingModel $shipping
     * @param PaymentModel $payment
     * @param AddressModel $address
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Quote $quote,
        Order $order,
        ShippingModel $shipping,
        PaymentModel $payment,
        AddressModel $address
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultFactory,
            $helper,
            $scopeConfig,
            $quote,
            $order,
            $shipping,
            $payment
        );
        $this->address = $address;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function update()
    {
        $addressId = $this->getAddressId();
        $addressData = $this->getAddressData();

        $this->address->loadAddress($addressId);
        $this->address->updateAddress($addressData);
    }

    /**
     * @param string[] $addressData
     * @return void
     */
    protected function updateUserAddress($addressData)
    {
        $applyForCustomer = $this->getRequest()
            ->getParam('apply_for_customer', false);

        if (!empty($applyForCustomer)) {
            $this->address->updateCustomerAddress($addressData);
        }
    }

    /**
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getAddressId()
    {
        $id = $this->getRequest()->getParam('address_id', null);
        if (empty($id)) {
            throw new \Exception('Empty param address_id');
        }

        return $id;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getAddressData()
    {
        $data = $this->getRequest()->getParams();

        if (isset($data['billing_address'])) {
            return $data['billing_address'];
        }

        if (isset($data['shipping_address'])) {
            return $data['shipping_address'];
        }

        throw new \Exception('Have not address data information');
    }
}
