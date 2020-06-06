<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 18.09.19
 * Time: 12:52
 */

namespace KozakGroup\RewriteOrderEditor\Plugin\Model\Order;

use Magento\Sales\Model\Order;

class AddUsernamePlugin
{

    protected $authSession;

    protected $customerSession;


    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->authSession = $authSession;
        $this->customerSession = $customerSession;
    }

    public function afterAddStatusHistoryComment(Order $subject, $result)
    {

        if(!$this->customerSession->isLoggedIn()){
            $result->setData('username',$this->authSession->getUser()->getUserName());
        }
        else{
            $result->setData('username','Customer');
        }

        return $result;
    }

}