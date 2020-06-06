<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 25.09.19
 * Time: 15:19
 */

namespace KozakGroup\RewriteOrderEditor\Plugin\Controller\Adminhtml;

use KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Edit\Items;
use Magento\Sales\Api\OrderManagementInterface as OrderManagement;
use MageWorx\OrderEditor\Model\Shipping;

class Reauthorize
{

    protected $orderRepository;

    protected $orderManagement;

    protected $document;

    protected $transactions;

    protected $shipping;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        OrderManagement $orderManagement,
        \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactions,
        \Magento\Framework\DataObject $document,
        Shipping $shipping
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->document = $document;
        $this->transactions = $transactions;
        $this->shipping = $shipping;
    }


    public function afterUpdate(Items $subject, $result)
    {
        $order = $this->orderRepository->get($subject->getRequest()->getParam('order_id', 0));

        $method = $order->getPayment();
        $transactions = $this->transactions->create()->addOrderIdFilter($order->getId());
        if(count($transactions->getItems()) > 1){
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
            $this->void($order);
            if(array_key_exists('public_hash',$method->getAdditionalInformation())){
                $method->setData('method_instance', null);
                $method->setMethod('pmclain_authorizenetcim_vault');
            }
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