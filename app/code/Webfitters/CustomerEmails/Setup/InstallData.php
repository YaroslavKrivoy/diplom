<?php
namespace Webfitters\CustomerEmails\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements InstallDataInterface {
    
    protected $customerSetupFactory;
    private $attributeSetFactory;
	
    public function __construct(CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory){
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
		$customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
		$attributeSetId = $customerEntity->getDefaultAttributeSetId();
		$attributeSet = $this->attributeSetFactory->create();
		$attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
		$customerSetup->addAttribute(Customer::ENTITY, 'customer_emails', [
			'type' => 'text',
			'label' => 'Customer Emails',
			'input' => 'textarea',
			'required' => false,
			'visible' => true,
			'user_defined' => false,
			'sort_order' => 999,
			'position' => 999,
			'system' => 0,
			'is_used_in_grid' => true,
			'note' => 'Comma separated list of emails for this customer.'
		]);
		$attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_emails')->addData([
			'attribute_set_id' => $attributeSetId,
			'attribute_group_id' => $attributeGroupId,
			'used_in_forms' => ['adminhtml_customer'],
		]);
		$attribute->save();
    }
	
}