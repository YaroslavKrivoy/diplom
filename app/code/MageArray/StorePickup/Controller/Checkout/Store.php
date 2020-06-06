<?php

namespace MageArray\StorePickup\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Store extends Action
{

    /**
     * Store constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation
     * @param MageArray\StorePickup\Model\StoreFactory $storeFactory
     * @param Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \MageArray\StorePickup\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->countryInformation = $countryInformation;
        $this->_storeFactory = $storeFactory;
        $this->_dataHelper = $dataHelper;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $html = '';
        $result = [];
        try {
            $storeColl = $this->_storeFactory->create()->load($post['storeId']);
            $disableDate = $this->_dataHelper->disablePickupDate();
            if (count($storeColl->getData()) > 0) {
                $country = $this->countryInformation
                    ->getCountryInfo($storeColl->getCountry());
                $countryName = $country->getFullNameLocale();
                $address = $storeColl->getAddress() . "<br/> " . $storeColl->getCity() . ", " . $storeColl->getState() . " " . $storeColl->getZipcode() . "<br/> " . $countryName . "<br/>" . $storeColl->getPhoneNumber();
                $html .= "<div><h4>Store Address:</h4><p> " . $storeColl->getStoreName() . "<br/>" . $address . "</p><p>Working Hours: " . $storeColl->getWorkingHours() . "</p></div>";
            }
            $slot = [];
            $additionalSlot = [];
            $timeSlot = $storeColl->getTimeSlot();
            if ($timeSlot) {
                $timeSlot = preg_split('/\r\n|\r|\n/', $timeSlot);
                foreach ($timeSlot as $dayTimeSlot) {
                    $dayTimeSlot = explode("=>", $dayTimeSlot);
                    if (null !== trim($dayTimeSlot[0]) && null !== trim($dayTimeSlot[1])) {
                        $slot[trim($dayTimeSlot[0])] = trim($dayTimeSlot[1]);
                    }
                }
            }

            $additionalTimeSlot = $storeColl->getAdditionalTimeSlot();
            if ($additionalTimeSlot) {
                $additionalTimeSlot = preg_split('/\r\n|\r|\n/', $additionalTimeSlot);
                foreach ($additionalTimeSlot as $dateTimeSlot) {
                    $dateTimeSlot = explode("=>", $dateTimeSlot);
                    if (null !==  trim($dateTimeSlot[0]) && null !== trim($dateTimeSlot[1])) {
                        $additionalSlot[trim($dateTimeSlot[0])] = trim($dateTimeSlot[1]);
                    }
                }
            }
            $result['html'] = $html;
            $result['success'] = 1;
            $result['disable_date'] = $disableDate;
            $result['working_days'] = $this->getWorkingDayArray($storeColl->getOpeningDays());
            $result['holidays'] = $storeColl->getHoliday();
            $result['timeslot'] = $slot;
            $result['additional_timeslot'] = $additionalSlot;
            $this->_checkoutSession->setData("store_session", $post['storeId']);
        } catch (\Exception $e) {
            $result['success'] = 0;
            $result['message'] = $e->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }

    public function getWorkingDayArray($days)
    {
        $allDays = "0,1,2,3,4,5,6";
        $workingDayArray = explode(",", $days);
        $all = explode(",", $allDays);
        $new = array_merge(array_diff($all, $workingDayArray), array_diff($workingDayArray, $all));
        return array_map('intval', $new);
    }
}
