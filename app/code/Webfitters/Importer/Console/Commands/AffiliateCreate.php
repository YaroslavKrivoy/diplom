<?php
namespace Webfitters\Importer\Console\Commands;

class AffiliateCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $affiliate;
	protected $customer;
	protected $address;
	protected $region;
	protected $group;
	protected $campaign;
	protected $hearabout;

	public function __construct(
		\Magento\Framework\App\State $state,
		\Mageplaza\Affiliate\Model\AccountFactory $affiliate,
		\Mageplaza\Affiliate\Model\GroupFactory $group,
		\Mageplaza\Affiliate\Model\CampaignFactory $campaign,
		\Magento\Customer\Model\CustomerFactory $customer,
		\Magento\Customer\Model\AddressFactory $address,
		\Magento\Directory\Model\RegionFactory $region,
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->affiliate = $affiliate;
		$this->customer = $customer;
		$this->address = $address;
		$this->region = $region;
		$this->group = $group;
		$this->campaign = $campaign;
		$this->hearabout = $hearabout;
	}

	protected function configure(){
		$this->setName('webfitters:affiliates')->setDescription('Imports affiliates from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$sources = $this->db->query('SELECT * FROM `gs_hear_about`;')->fetchAll(\PDO::FETCH_OBJ);
		$affiliates = $this->db->query('SELECT * FROM `gs_affiliate`;')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($affiliates) + count($sources));
		foreach($sources as $source){
			$hearabout = $this->hearabout->create()->getCollection()->addFieldToFilter('name', $source->description)->getFirstItem();
			if(!$hearabout->getId()){
				$hearabout = $this->hearabout->create();
				$hearabout->setName($source->description);
				$hearabout->save();
			}
			$progress->advance();
		}
		foreach($affiliates as $affiliate){
			$customer = $this->customer->create()->setWebsiteId(1)->loadByEmail($affiliate->email);
			if(!$customer->getEntityId()){
				$customer = $this->customer->create()->setWebsiteId(1);
				$customer->setPassword(bin2hex(openssl_random_pseudo_bytes(5)));
				$customer->setEmail($affiliate->email);
			} 
			$customer->setFirstname($affiliate->firstname);
			$customer->setLastname($affiliate->lastname);
			$customer->setCreatedAt(date('Y-m-d H:i:s', strtotime($affiliate->created)));
			$customer->setGroupId(4);
			$customer->save();
			$cd = $customer->getDataModel();
			$cd->setCustomAttribute('company', $affiliate->company);
			$customer->updateData($cd);
			$customer->save();
			$current = $customer->getAddresses();
			if(count($current) == 0){
				$addr = $this->address->create();
				$addr->setIsDefaultBilling(1);
				$addr->setIsDefaultShipping(1);
				$addr->setCustomerId($customer->getId());
				$addr->setFirstname((($affiliate->firstname=='')?$customer->getFirstname():$affiliate->firstname));
				$addr->setLastname((($affiliate->lastname=='')?$customer->getLastname():$affiliate->lastname));
				$addr->setCountryId((($affiliate->country=='USA'||$affiliate->country=='')?'US':$affiliate->country));
				$addr->setPostcode($affiliate->zip);
				$addr->setCity($affiliate->city);
				$addr->setTelephone($affiliate->phone);
				$addr->setFax($affiliate->fax);
				$addr->setRegion($this->region->create()->loadByCode($affiliate->state, (($affiliate->country=='USA'||$affiliate->country=='')?'US':$affiliate->country))->getId());
				$addr->setStreet($affiliate->address1.' '.$affiliate->address2);
				$addr->setSaveInAddressBook(true);
				$addr->save();
			}

			/* check and create group here somehow... */
			$group = $this->group->create()->getCollection()->addFieldToFilter('name', number_format($affiliate->commission_percent, 1).'% Group')->getFirstItem();
			if(!$group->getGroupId()){
				$group = $this->group->create();
				$group->setName(number_format($affiliate->commission_percent, 1).'% Group');
				$group->save();
			}
			$campaign = $this->campaign->create()->getCollection()->addFieldToFilter('affiliate_group_ids', $group->getId())->getFirstItem();
			if(!$campaign->getCampaignId()){
				$campaign = $this->campaign->create();
			}
			$campaign->setName(number_format($affiliate->commission_percent, 1).'% Group');
			$campaign->setDescription('Members of this group receive '.number_format($affiliate->commission_percent, 1).'% commision');
			$campaign->setStatus(1);
			$campaign->setWebsiteIds(1);
			$campaign->setAffiliateGroupIds($group->getId());
			$campaign->setFromDate(null);
			$campaign->setToDate(null);
			$campaign->setDisplay(2);
			$campaign->setSortOrder(0);
			$campaign->setCommission([
				'tier_1' => [
					'type' => '1',
					'value' => number_format($affiliate->commission_percent, 1),
					'type_second' => '1',
					'value_second' => '',
					'name' => 'Tier 1'
				]
			]);
			$campaign->setDiscountAction('by_percent');
			$campaign->setDiscountAmount(0);
			$campaign->setDiscountQty(0);
			$campaign->setDiscountStep(0);
			$campaign->setDiscountDescription(null);
			$campaign->setFreeShipping(null);
			$campaign->setApplyToShipping(0);
			$campaign->save();


			$test = $this->affiliate->create()->getCollection()->addFieldToFilter('code', $affiliate->affiliate_code)->getFirstItem();
			if(!$test->getAccountId()){
				$aff = $this->affiliate->create();
			} else {
				$aff = $this->affiliate->create()->load($test->getAccountId());
			}
			$aff->setCustomerId($customer->getId());
			$aff->setCode($affiliate->affiliate_code);
			$aff->setGroupId($group->getId());
			$aff->setBalance(0);
			$aff->setHoldingBalance(0);
			$aff->setTotalCommission(0);
			$aff->setTotalPaid(0);
			$aff->setStatus(1);
			$aff->setEmailNotificiation(0);
			$aff->setWithdrawMethod(null);
			$aff->setWithdrawInformation(null);
			$aff->save();

			$progress->advance();
		}
		$progress->finish();
	}

}