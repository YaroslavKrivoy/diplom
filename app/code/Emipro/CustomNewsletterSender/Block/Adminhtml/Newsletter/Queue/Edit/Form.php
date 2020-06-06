<?php
namespace Emipro\CustomNewsletterSender\Block\Adminhtml\Newsletter\Queue\Edit;
class Form extends \Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form {
protected function _prepareForm()
    {
    		 $queue = $this->getQueue();
       		 $form = $this->_formFactory->create();
      		 $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Queue Information'), 'class' => 'fieldset-wide']
        );
      		 $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );
        if ($queue->getQueueStatus() == \Magento\Newsletter\Model\Queue::STATUS_NEVER) {
            $fieldset->addField(
                'date',
                'date',
                [
                    'name' => 'start_at',
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'label' => __('Queue Date Start')
                ]
            );

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField(
                    'stores',
                    'multiselect',
                    [
                        'name' => 'stores[]',
                        'label' => __('Subscribers From'),
                        'values' => $this->_systemStore->getStoreValuesForForm(),
                        'value' => $queue->getStores()
                    ]
                );
            } else {
                $fieldset->addField(
                    'stores',
                    'hidden',
                    ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
                );
            }
        } else {
            $fieldset->addField(
                'date',
                'date',
                [
                    'name' => 'start_at',
                    'disabled' => 'true',
                    'style' => 'width:38%;',
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'label' => __('Queue Date Start')
                ]
            );

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField(
                    'stores',
                    'multiselect',
                    [
                        'name' => 'stores[]',
                        'label' => __('Subscribers From'),
                        'required' => true,
                        'values' => $this->_systemStore->getStoreValuesForForm(),
                        'value' => $queue->getStores()
                    ]
                );
            } else {
                $fieldset->addField(
                    'stores',
                    'hidden',
                    ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
                );
            }
        }

         if ($queue->getQueueStartAt()) {
            $form->getElement(
                'date'
            )->setValue(
                $this->_localeDate->date(new \DateTime($queue->getQueueStartAt()))
            );
        }
        $fieldset->addField(
            'subject',
            'text',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSubject() : $queue
                    ->getNewsletterSubject()
            ]
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $c=$objectManager->create('Magento\Customer\Model\Group')->getCollection()->getData();
        $value_array = array();	
		$value_array['-1'] = 'All';
		foreach($c as $cc)
		{
			$value_array[$cc['customer_group_id']] = $cc['customer_group_code'];
		}
		$fieldset->addField('customergroup', 'select', [
			'label'     => __('Customer Group'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'customergroup',
			'values' => $value_array,
			'value'	=> $queue->getCustomerGroup(),
			'disabled' => false,
			'readonly' => false,
			'note' => '<small>Selected customer group will receive email.</small>'
        ] );
         $fieldset->addField(
            'sender_name',
            'text',
            [
                'name' => 'sender_name',
                'label' => __('Sender Name'),
                'title' => __('Sender Name'),
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSenderName() : $queue
                    ->getNewsletterSenderName()
            ]
        );

        $fieldset->addField(
            'sender_email',
            'text',
            [
                'name' => 'sender_email',
                'label' => __('Sender Email'),
                'title' => __('Sender Email'),
                'class' => 'validate-email',
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSenderEmail() : $queue
                    ->getNewsletterSenderEmail()
            ]
        );

        $widgetFilters = ['is_email_compatible' => 1];
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['widget_filters' => $widgetFilters]);
        if ($queue->isNew()) {
            $fieldset->addField(
                'text',
                'editor',
                [
                    'name' => 'text',
                    'label' => __('Message'),
                    'state' => 'html',
                    'required' => true,
                    'value' => $queue->getTemplate()->getTemplateText(),
                    'style' => 'height: 600px;',
                    'config' => $wysiwygConfig
                ]
            );

            $fieldset->addField(
                'styles',
                'textarea',
                [
                    'name' => 'styles',
                    'label' => __('Newsletter Styles'),
                    'container_id' => 'field_newsletter_styles',
                    'value' => $queue->getTemplate()->getTemplateStyles()
                ]
            );
        } elseif (\Magento\Newsletter\Model\Queue::STATUS_NEVER != $queue->getQueueStatus()) {
            $fieldset->addField(
                'text',
                'textarea',
                ['name' => 'text', 'label' => __('Message'), 'value' => $queue->getNewsletterText()]
            );

            $fieldset->addField(
                'styles',
                'textarea',
                ['name' => 'styles', 'label' => __('Newsletter Styles'), 'value' => $queue->getNewsletterStyles()]
            );

            $form->getElement('text')->setDisabled('true')->setRequired(false);
            $form->getElement('styles')->setDisabled('true')->setRequired(false);
            $form->getElement('subject')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_name')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_email')->setDisabled('true')->setRequired(false);
            $form->getElement('stores')->setDisabled('true');
        } else {
            $fieldset->addField(
                'text',
                'editor',
                [
                    'name' => 'text',
                    'label' => __('Message'),
                    'state' => 'html',
                    'required' => true,
                    'value' => $queue->getNewsletterText(),
                    'style' => 'height: 600px;',
                    'config' => $wysiwygConfig
                ]
            );

            $fieldset->addField(
                'styles',
                'textarea',
                [
                    'name' => 'styles',
                    'label' => __('Newsletter Styles'),
                    'value' => $queue->getNewsletterStyles(),
                    'style' => 'height: 300px;'
                ]
            );
        }

        $this->setForm($form);
        return $this;

    }

}
