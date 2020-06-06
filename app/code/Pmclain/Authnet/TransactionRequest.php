<?php

namespace Pmclain\Authnet;

use Pmclain\Authnet\PaymentProfile\Address;
use Pmclain\Authnet\PaymentProfile\Payment\PaymentInterface;
use Pmclain\Authnet\TransactionRequest\LineItem;
use Pmclain\Authnet\TransactionRequest\TaxDutyShipping;
use Pmclain\Authnet\TransactionRequest\TransactionType;
use Pmclain\Authnet\TransactionRequest\Order;
use Pmclain\Authnet\CustomerProfile;

class TransactionRequest
{
    const FIELD_TRANSACTION_TYPE = 'transactionType';
    const FIELD_AMOUNT = 'amount';
    const FIELD_REF_TRANS_ID = 'refTransId';
    const FIELD_PAYMENT = 'payment';
    const FIELD_PROFILE = 'profile';
    const FIELD_CUSTOMER_PROFILE_ID = 'customerProfileId';
    const FIELD_PAYMENT_PROFILE = 'paymentProfile';
    const FIELD_PAYMENT_PROFILE_ID = 'paymentProfileId';
    const FIELD_CREATE_PROFILE = 'createProfile';
    const FIELD_SOLUTION = 'solution';
    const FIELD_SOLUTION_ID = 'id';
    const FIELD_ORDER = 'order';
    const FIELD_LINE_ITEMS = 'lineItems';
    const FIELD_LINE_ITEM = 'lineItem';
    const FIELD_TAX = 'tax';
    const FIELD_DUTY = 'duty';
    const FIELD_SHIPPING = 'shipping';
    const FIELD_TAX_EXEMPT = 'taxExempt';
    const FIELD_PO_NUMBER = 'poNumber';
    const FIELD_CUSTOMER = 'customer';
    const FIELD_BILL_TO = 'billTo';
    const FIELD_SHIP_TO = 'shipTo';
    const FIELD_CUSTOMER_IP = 'customerIp';

    /**
     * @var string
     */
    private $transactionType;

    /**
     * @var int|float
     */
    private $amount;

    /**
     * @var PaymentInterface
     */
    private $payment;

    /**
     * @var string|int
     */
    private $customerProfileId;

    /**
     * @var string|int
     */
    private $paymentProfileId;

    /**
     * @var bool
     */
    private $createProfile = false;

    /**
     * @var string
     */
    private $solutionId;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var LineItem[]
     */
    private $lineItems = [];

    /**
     * @var TaxDutyShipping
     */
    private $tax;

    /**
     * @var TaxDutyShipping
     */
    private $duty;

    /**
     * @var TaxDutyShipping
     */
    private $shipping;

    /**
     * @var bool
     */
    private $taxExempt = false;

    /**
     * @var string
     */
    private $poNumber;

    /**
     * @var CustomerProfile
     */
    private $customer;

    /**
     * @var Address
     */
    private $billTo;

    /**
     * @var Address
     */
    private $shipTo;

    /**
     * @var string
     */
    private $customerIp;

    /**
     * @var string
     */
    private $refTransId;

    /**
     * @param string $type
     * @return $this
     */
    public function setTransactionType($type)
    {
        TransactionType::validate($type);
        $this->transactionType = $type;
        return $this;
    }

    /**
     * @param int|float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $refTransId
     * @return $this
     */
    public function setRefTransId($refTransId)
    {
        $this->refTransId = $refTransId;
        return $this;
    }

    /**
     * @param PaymentInterface $payment
     * @return $this
     */
    public function setPayment(PaymentInterface $payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @param string|int $customerProfileId
     * @return $this
     */
    public function setCustomerProfileId($customerProfileId)
    {
        $this->customerProfileId = $customerProfileId;
        return $this;
    }

    /**
     * @param string|int $paymentProfileId
     * @return $this
     */
    public function setPaymentProfileId($paymentProfileId)
    {
        $this->paymentProfileId = $paymentProfileId;
        return $this;
    }

    /**
     * @param bool $create
     * @return $this
     */
    public function setCreateProfile(bool $create)
    {
        $this->createProfile = $create;
        return $this;
    }

    /**
     * @param string $solutionId
     * @return $this
     */
    public function setSolutionId(string $solutionId)
    {
        $this->solutionId = $solutionId;
        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param LineItem $lineItem
     * @return $this
     */
    public function addLineItem(LineItem $lineItem)
    {
        $this->lineItems[] = $lineItem;
        return $this;
    }

    /**
     * @param TaxDutyShipping $tax
     * @return $this
     */
    public function setTax(TaxDutyShipping $tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @param TaxDutyShipping $duty
     * @return $this
     */
    public function setDuty(TaxDutyShipping $duty)
    {
        $this->duty = $duty;
        return $this;
    }

    /**
     * @param TaxDutyShipping $shipping
     * @return $this
     */
    public function setShipping(TaxDutyShipping $shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    /**
     * @param bool $taxExempt
     * @return $this
     */
    public function setTaxExempt(bool $taxExempt)
    {
        $this->taxExempt = $taxExempt;
        return $this;
    }

    /**
     * @param string $poNumber
     * @return $this
     */
    public function setPoNumber($poNumber)
    {
        $this->poNumber = $poNumber;
        return $this;
    }

    /**
     * @param CustomerProfile $customer
     * @return $this
     */
    public function setCustomer(CustomerProfile $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param Address $billTo
     * @return $this
     */
    public function setBillTo(Address $billTo)
    {
        $this->billTo = $billTo;
        return $this;
    }

    /**
     * @param Address $shipTo
     * @return $this
     */
    public function setShipTo(Address $shipTo)
    {
        $this->shipTo = $shipTo;
        return $this;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setCustomerIp($ip)
    {
        $this->customerIp = $ip;
        return $this;
    }

    public function toArray()
    {
        $body = [];
        $body[self::FIELD_TRANSACTION_TYPE] = $this->transactionType;
        if (isset($this->amount)) {
            $body[self::FIELD_AMOUNT] = $this->amount;
        }
        if (isset($this->payment)) {
            $body[self::FIELD_PAYMENT][$this->payment->getKey()] = $this->payment->toArray();
        }
        if (isset($this->refTransId)) {
            $body[self::FIELD_REF_TRANS_ID] = $this->refTransId;
        }
        if (isset($this->customerProfileId)) {
            $body[self::FIELD_PROFILE][self::FIELD_CUSTOMER_PROFILE_ID] = $this->customerProfileId;
        }
        if (isset($this->paymentProfileId)) {
            $body[self::FIELD_PROFILE][self::FIELD_PAYMENT_PROFILE][self::FIELD_PAYMENT_PROFILE_ID] = $this->paymentProfileId;
        }
        if ($this->createProfile) {
            $body[self::FIELD_PROFILE][self::FIELD_CREATE_PROFILE] = $this->createProfile;
        }
        if (isset($this->solutionId)) {
            $body[self::FIELD_SOLUTION][self::FIELD_SOLUTION_ID] = $this->solutionId;
        }
        if (isset($this->order)) {
            $body[self::FIELD_ORDER] = $this->order->toArray();
        }
        if (isset($this->tax)) {
            $body[self::FIELD_TAX] = $this->tax->toArray();
        }
        if (isset($this->duty)) {
            $body[self::FIELD_DUTY] = $this->duty->toArray();
        }
        if (isset($this->shipping)) {
            $body[self::FIELD_SHIPPING] = $this->shipping->toArray();
        }
        if (isset($this->poNumber)) {
            $body[self::FIELD_PO_NUMBER] = $this->poNumber;
        }
        if (isset($this->customer)) {
            $body[self::FIELD_CUSTOMER] = $this->customer->toArray();
        }
        if (isset($this->billTo)) {
            $body[self::FIELD_BILL_TO] = $this->billTo->toArray();
        }
        if (isset($this->shipTo)) {
            $body[self::FIELD_SHIP_TO] = $this->shipTo->toArray();
        }
        if (isset($this->customerIp)) {
            $body[self::FIELD_CUSTOMER_IP] = $this->customerIp;
        }

        return $body;
    }
}
