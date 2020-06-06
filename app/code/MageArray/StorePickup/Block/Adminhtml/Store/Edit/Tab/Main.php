<?php
namespace MageArray\StorePickup\Block\Adminhtml\Store\Edit\Tab;

/**
 * Class Main
 * @package MageArray\StorePickup\Block\Adminhtml\Store\Edit\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Locale\Weekdays $weekDays,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_weekDays = $weekDays;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('store_post');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Store Information')]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'storepickup_id',
                'hidden',
                ['name' => 'storepickup_id']
            );
        }

        $fieldset->addField(
            'store_name',
            'text',
            [
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'name' => 'store_name',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'phone_number',
            'text',
            [
                'name' => 'phone_number',
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'opening_days',
            'multiselect',
            [
                'label' => __('Working Days'),
                'title' => __('Working Days'),
                'name' => 'opening_days',
                'values' => $this->_weekDays->toOptionArray(),
                'required' => true,
            ]
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_view_ids',
                'multiselect',
                [
                    'name' => 'store_view_ids',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore
                        ->getStoreValuesForForm(false, true)
                ]
            );
        } else {
            $fieldset->addField(
                'store_view_id',
                'hidden',
                [
                    'name' => 'stores[]',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'working_hours',
            'text',
            [
                'label' => __('Working Hours'),
                'title' => __('Working Hours'),
                'name' => 'working_hours',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'holiday',
            'text',
            [
                'label' => __('Date of Holiday'),
                'title' => __('Date of Holiday'),
                'name' => 'holiday',
                'required' => false,
                'note' => "Format:m/d/yyyy, e.g:5/9/2018,5/23/2018"
            ]
        );

        $fieldset->addField(
            'time_slot',
            'textarea',
            [
                'label' => __('Time Slot'),
                'title' => __('Time Slot'),
                'name' => 'time_slot',
                'required' => false,
                'note' => "Dayname=>Timeslot, e.g:<br/>Tuesday=>11:00AM-12:00PM,12:00PM-2:00PM<br/>Wednesday=>12:00PM-2:00PM,4:00PM-6:30PM"
            ]
        );

        $fieldset->addField(
            'additional_time_slot',
            'textarea',
            [
                'label' => __('Additional Time Slot'),
                'title' => __('Additional Time Slot'),
                'name' => 'additional_time_slot',
                'required' => false,
                'note' => "Date=>Timeslot,  date format : m/d/yyyy, e.g:<br/>5/16/2018=>11:00AM-1:00PM,3:00PM-6:30PM<br/>5/29/2018=>1:00PM-2:00PM,4:00PM-5:30PM"
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'name' => 'sort_order',
            ]
        );

        $fieldsetSecond = $form->addFieldset(
            'base_fieldset_second',
            ['legend' => __('Store Address')]
        );

        $fieldsetSecond->addField(
            'address',
            'text',
            [
                'label' => __('Address'),
                'title' => __('Address'),
                'name' => 'address',
                'required' => true,
            ]
        );

        $fieldsetSecond->addField(
            'zipcode',
            'text',
            [
                'label' => __('Zipcode'),
                'title' => __('Zipcode'),
                'name' => 'zipcode',
                'required' => true,
            ]
        );

        $fieldsetSecond->addField(
            'city',
            'text',
            [
                'label' => __('City'),
                'title' => __('City'),
                'name' => 'city',
                'required' => true,
            ]
        );

        $optionsc=$this->_countryFactory->toOptionArray();
        $country = $fieldsetSecond->addField(
                'country',
                'select',
                [
                    'name' => 'country',
                    'label' => __('Country'),
                    'title' => __('Country'),
                    'values' => $optionsc,
                    'class' => 'address-country',
                    'required' => true,
                ]
            );
        $fieldsetSecond->addField(
                'state_id',
                'select',
                [
                    'name' => 'state_id',
                    'label' => __('State/Province Or Region'),
                    'title' => __('State/Province Or Region'),
                    'values' =>  ['--Please Select Country--'],
                    'class' => 'address-state-id',
                    'required' => true,
                ]
            );
        $fieldsetSecond->addField(
                'state',
                'text',
                [
                    'name' => 'state',
                    'label' => __('State/Province'),
                    'title' => __('State/Province'),
                    'class' => 'address-state',
                    'required' => true,
                ]
            );

        /*
          * Add Ajax to the Country select box html output
          */

        $country->setAfterElementHtml(
            "   
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
					$(document).ready(function(){
						if($('.address-country').val()){
							getRegion();
						}
					});
					$('#edit_form').on('change', '.address-country', function(event){
                       getRegion();
					});
					function getRegion(){
						$.ajax({
							url : '" . $this->getUrl('storepickup/lists/regionlist') . "country/' +  $('.address-country').val(),
							type: 'get',
							dataType: 'json',
							showLoader:true,
							success: function(data){
								if(data.htmlconent==''){
									$('.address-state-id').empty();
									$('.field-state_id').css('display', 'none');
									$('.field-state').css('display', 'block');
									if($('.address-country').val() && $('.address-country').val()=='" . $model->getCountry() . "'){
										$('.address-state').val('" . $model->getState() . "');
									}
									$('.field-state').addClass('_required required');
									$('.address-state').addClass('required-entry _required');
									$('.field-state_id').removeClass('_required required');
									$('.address-state-id').removeClass('required-entry _required');
								}else{
									$('.address-state-id').empty();
									$('.address-state-id').append(data.htmlconent);
									$('.field-state_id ').css('display', 'block');
									$('.address-state').attr('value','');
									$('.field-state').css('display', 'none');
									if($('.address-country').val() && $('.address-country').val()=='" . $model->getCountry() . "'){
										$('.address-state-id').val('" . $model->getState() . "');
									}
									$('.field-state').removeClass('_required required');
									$('.address-state').removeClass('required-entry _required');
									$('.field-state_id').addClass('_required required');
									$('.address-state-id').addClass('required-entry _required');
								}
							}
                        });
					}
                }
            );
            </script>"
        );
        $fieldsetSecond->addField(
            'latitude',
            'text',
            [
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'name' => 'latitude',
            ]
        );

        $fieldsetSecond->addField(
            'longitude',
            'text',
            [
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'name' => 'longitude',
            ]
        );

        $googleMap = $this->getLayout()
            ->createBlock('MageArray\StorePickup\Block\Adminhtml\Store\Edit\Tab\Map');

        $fieldsetSecond->addField(
            'map',
            'text',
            [
                'label' => __('Store Map'),
                'name' => 'map',
            ]
        )->setRenderer($googleMap);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Store Information');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Store Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @param $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
