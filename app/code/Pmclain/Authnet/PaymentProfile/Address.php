<?php

namespace Pmclain\Authnet\PaymentProfile;

class Address
{
    const FIELD_FIRSTNAME = 'firstName';
    const FIELD_LASTNAME = 'lastName';
    const FIELD_COMPANY = 'company';
    const FIELD_ADDRESS = 'address';
    const FIELD_CITY = 'city';
    const FIELD_STATE = 'state';
    const FIELD_ZIP = 'zip';
    const FIELD_COUNTRY = 'country';
    const FIELD_PHONE_NUMBER = 'phoneNumber';
    const FIELD_FAX_NUMBER = 'faxNumber';

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $faxNumber;

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @param string $company
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param string $zip
     * @return $this
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param string $faxNumber
     * @return $this
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        if ($this->firstname) {
            $result[self::FIELD_FIRSTNAME] = $this->firstname;
        }
        if ($this->lastname) {
            $result[self::FIELD_LASTNAME] = $this->lastname;
        }
        if ($this->company) {
            $result[self::FIELD_COMPANY] = $this->company;
        }
        if ($this->address) {
            $result[self::FIELD_ADDRESS] = $this->address;
        }
        if ($this->city) {
            $result[self::FIELD_CITY] = $this->city;
        }
        if ($this->state) {
            $result[self::FIELD_STATE] = $this->state;
        }
        if ($this->zip) {
            $result[self::FIELD_ZIP] = $this->zip;
        }
        if ($this->country) {
            $result[self::FIELD_COUNTRY] = $this->country;
        }
        if ($this->phoneNumber) {
            $result[self::FIELD_PHONE_NUMBER] = $this->phoneNumber;
        }
        if ($this->faxNumber) {
            $result[self::FIELD_FAX_NUMBER] = $this->faxNumber;
        }

        return $result;
    }
}
