<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 02.10.19
 * Time: 14:14
 */

namespace KozakGroup\RewriteOrderEditor\Plugin\Controller\Adminhtml;

use KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Edit\Payment;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;

class AuthorizeAfterChangePayment
{

    protected $orderRepository;

    protected $orderManagement;

    protected $document;

    protected $transactions;

    protected $ccCongig;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        OrderManagement $orderManagement,
        \Magento\Framework\DataObject $document,
        \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactions,
        \Magento\Payment\Model\CcConfig $ccCongig

    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->document = $document;
        $this->transactions = $transactions;
        $this->ccCongig = $ccCongig;
    }


    public function afterUpdate(Payment $subject, $result){

        $order = $this->orderRepository->get($subject->getRequest()->getParam('order_id', 0));
        $method = $order->getPayment();
        $transactions = $this->transactions->create()->addOrderIdFilter($order->getId());
        if($transactions->getItems()){
            $this->void($order);
        }
        if(!empty($subject->getRequest()->getParam('pmclain_authorizenetcim_cc_number')) && count($transactions->getItems()) > 1){
            $addInfo = $method->getAdditionalInformation();
            $addInfo['cc_type'] = $method->getCcType();
            $addInfo['cc_exp_month'] = $method->getCcExpMonth();
            $addInfo['cc_exp_year'] = $method->getCcExpYear();
            $addInfo['cc_last4'] = $method->getCcLast4();
            $method->setAdditionalInformation($addInfo);
            $order->setPayment($method);
            $order->save();
        }

        if($method->getMethod() == 'pmclain_authorizenetcim'){

            if(array_key_exists('public_hash',$method->getAdditionalInformation())){
                $method->setMethod('pmclain_authorizenetcim_vault');
            }
            $method->setData('method_instance', null);
            $method->authorize(true,$order->getGrandTotal());
            $order->save();
        }
        return $result;

    }

    protected function void($order)
    {
        $method = $order->getPayment();
        $method->void($this->document);
    }

}