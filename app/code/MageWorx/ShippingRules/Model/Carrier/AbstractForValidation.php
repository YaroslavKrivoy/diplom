<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

class AbstractForValidation extends AbstractCarrier
{
    /**
     * @var string
     */
    protected $_code = 'mageworx-shipping';

    public function isShippingLabelsAvailable(){
        return true;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return array
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        return [];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [];
    }

    public function requestToShipment($request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = [];
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new \Magento\Framework\DataObject($package['params']));
            $request->setPackageItems($package['items']);
            $tracking = bin2hex(openssl_random_pseudo_bytes(6));
            $data[] = [
                'tracking_number' => $tracking,
                'label_content' => ''
            ];
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($tracking);
                $isFirstRequest = false;
            }
        }

        $response = new \Magento\Framework\DataObject(['info' => $data]);
        /*if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }*/

        return $response;
    }
}
