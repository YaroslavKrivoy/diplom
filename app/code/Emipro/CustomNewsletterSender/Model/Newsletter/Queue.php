<?php
namespace Emipro\CustomNewsletterSender\Model\Newsletter;
class Queue extends \Magento\Newsletter\Model\Queue {
 public function sendPerSubscriber($count = 20)
    {	
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($this->getQueueStatus() != self::STATUS_SENDING &&
            ($this->getQueueStatus() != self::STATUS_NEVER &&
            $this->getQueueStartAt())
        ) {
            return $this;
        }

        if (!$this->_subscribersCollection->getQueueJoinedFlag()) {
            $this->_subscribersCollection->useQueue($this);
        }

        if ($this->_subscribersCollection->getSize() == 0) {
            $this->_finishQueue();
            return $this;
        }
        
        $collection = $this->_subscribersCollection->useOnlyUnsent()->showCustomerInfo()->setPageSize(
            $count
        )->setCurPage(
            1
        );
         $customer_group_id = $this->getCustomerGroup();

         if($customer_group_id != -1) {
              if($customer_group_id == 0)
              {
                  $collection->addFieldToFilter("customer_id",array("eq"=>0));
              }else{
            $collection->getSelect()->join(array('soas'=>'customer_entity'),'main_table.customer_id=soas.entity_id AND soas.group_id='.$customer_group_id); 
        }
            
        }
                $collection->load();        

        $this->_transportBuilder->setTemplateData(
            [
                'template_subject' => $this->getNewsletterSubject(),
                'template_text' => $this->getNewsletterText(),
                'template_styles' => $this->getNewsletterStyles(),
                'template_filter' => $this->_templateFilter,
                'template_type' => self::TYPE_HTML,
            ]
        );      
        foreach ($collection->getItems() as $item) {       
            $transport = $this->_transportBuilder->setTemplateOptions(
                ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $item->getStoreId()]
            )->setTemplateVars(
                ['subscriber' => $item]
            )->setFrom(
                ['name' => $this->getNewsletterSenderEmail(), 'email' => $this->getNewsletterSenderName()]
            )->addTo(
                $item->getSubscriberEmail(),
                $item->getSubscriberFullName()
            )->getTransport();

            try {

                  $transport->sendMessage();

              }  catch (\Magento\Framework\Exception\MailException $e) {
           
                $problem = $this->_problemFactory->create();
                $problem->addSubscriberData($item);
                $problem->addQueueData($this);
                $problem->addErrorData($e);
                $problem->save();
            }
            $item->received($this);
        }

        if (count($collection->getItems()) < $count - 1 || count($collection->getItems()) == 0) {
            $this->_finishQueue();
        }
        return $this;
          
    }

}