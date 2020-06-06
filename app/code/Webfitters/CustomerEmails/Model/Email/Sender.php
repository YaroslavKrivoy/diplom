<?php
namespace Webfitters\CustomerEmails\Model\Email;

class Sender extends \Magento\Sales\Model\Order\Email\SenderBuilder {

    public function send() {
        $cust = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Customer\Model\CustomerFactory');
        $customer = $cust->create()->setWebsiteId(1)->loadByEmail($this->identityContainer->getCustomerEmail());
        $extras = [];
        if($customer->getCustomerEmails() && strlen($customer->getCustomerEmails()) > 0){
            $extras = array_map('trim', explode(',', $customer->getCustomerEmails()));
        }
        $this->configureEmailTemplate();
        $this->transportBuilder->addTo($this->identityContainer->getCustomerEmail(), $this->identityContainer->getCustomerName());
        foreach($extras as $extra){
            if($extra != ''){
                $this->transportBuilder->addTo($extra, $this->identityContainer->getCustomerName());
            }
        }


        $copyTo = $this->identityContainer->getEmailCopyTo();
        if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }

}
