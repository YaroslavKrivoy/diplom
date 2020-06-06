<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageplaza\Affiliate\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		$setup->getConnection()->insertMultiple(
			$setup->getTable('mageplaza_affiliate_group'),
			[
				['group_id' => '1', 'name' => 'General', 'created_at' => date('Y-m-d')],
				['group_id' => '2', 'name' => 'Bronze', 'created_at' => date('Y-m-d')],
				['group_id' => '3', 'name' => 'Silver', 'created_at' => date('Y-m-d')],
				['group_id' => '4', 'name' => 'Gold', 'created_at' => date('Y-m-d')],
				['group_id' => '5', 'name' => 'Platinum', 'created_at' => date('Y-m-d')]
			]
		);

		$setup->getConnection()->insert($setup->getTable('mageplaza_affiliate_campaign'), $this->getCampaignDefaultData());

		$this->insertBlock($setup);

		$setup->endSetup();
	}

	public function getCampaignDefaultData()
	{
		return [
			'name'                => 'Default Campaign',
			'description'         => 'This is a sample campaign',
			'status'              => \Mageplaza\Affiliate\Model\Campaign\Status::ENABLED,
			'website_ids'         => '1',
			'affiliate_group_ids' => '1,2,3,4,5',
			'display'             => \Mageplaza\Affiliate\Model\Campaign\Display::ALLOW_GUEST,
			'created_at'          => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
		];
	}

	public function insertBlock($setup)
	{
		$blocks = $this->getDataBlock();
		foreach ($blocks as $block) {
			$setup->getConnection()->delete($setup->getTable('cms_block'), ['identifier = ?' => $block['identifier']]);
		}
		$setup->getConnection()->insertMultiple($setup->getTable('cms_block'), $blocks);

		return $this;
	}

	public function getDataBlock()
	{
		$homecontent  = '<h3 style="font-weight: bold;">Welcome to our Affiliate Network!</h3>
<p style="margin-bottom: 20px;">We are so excited to introduce the easy and profitable business model on Internet to you. It is called Affiliate Program. The most special in Affiliate is you can still receive commission without any products, investment cost or personal website. Our program also don&rsquo;t ask you any experiences in business or technical knowledge. Starting with Affiliate is the ideal starting for the beginners.</p>
<h3 style="font-weight: bold;">How does it work?</h3>
<p style="margin-bottom: 20px;">When you come to Affiliate Program, you just create a new account totally freely for your work. Then you will use available banner, email or text link to sale our website to whoever you want. Commission will be given to you if person you refer clicks on one of our website&rsquo;s links. After a successful purchase, you will receive commission.</p>
<h3 style="font-weight: bold;">Controlling your work directly!</h3>
<p style="margin-bottom: 20px;">Affiliate Program makes you more active in business. You can check your account balance and track directly own transaction anytime.</p>';
		$refercontent = '<h3>Share more, earn more!</h3>
  <h3>How it works:</h3>
  <ul style="margin-left: 15px; list-style-type: disc; margin-bottom: 20px;">
    <li>Create your referral link.</li>
    <li>Share our link to your friends by using our template.</li>
    <li>Paid commision per click, action and purchase.</li>
  </ul>';
		$termcontent  = '<p>By filling out the signup form you acknowledge that you have read the terms and conditions, understand, and agree with them.</p>
<h3>Joining the Program</h3>
<p>By filling out the signup form, and upon acceptance, you will become an affiliate and are bound by the terms of this agreement. Your participation in the program is solely for this purpose: to legally advertise our website to receive a commission on products purchased by your referred individuals.</p>
<h3>Affiliate Responsibilities</h3>
<p>It is understood that you will introduce our products to your current and prospective customers and will comply with all laws as well those that govern email marketing and anti-spam laws. {{config path="general/store_information/name"}} reserves the right to accept or reject any prospective customers and will pay you a commission per customer referred using your affiliate code according to the designated payment schedule.</p>
<p>Either you or {{config path="general/store_information/name"}} may terminate the Affiliate relationship at any time. You are only eligible to earn Affiliate payments during your time as an approved Affiliate. {{config path="general/store_information/name"}} may change the program or service policies and operating procedures at any time.</p>
<h3>Affiliate Relationship</h3>
<p>This Affiliate relationship is one of independent contractors. {{config path="general/store_information/name"}} will not be liable for indirect, special or consequential damages arising in connection with this program and our aggregate liability arising with respect to this program will not exceed the total referral fees paid or payable to you. Interspire makes no express or implied warranties or representations with respect to the program. In addition, {{config path="general/store_information/name"}} makes no representation that the operation of the service will be uninterrupted or error-free, and {{config path="general/store_information/name"}} will not be liable for the consequences of any interruptions or errors.</p>';

		return [
			[
				'title'      => __('Affiliate Welcome homepage content'),
				'identifier' => 'affiliate-home',
				'content'    => $homecontent,
				'is_active'  => 1
			],
			[
				'title'      => __('Affiliate referfriend description'),
				'identifier' => 'affiliate-referfriend-description',
				'content'    => $refercontent,
				'is_active'  => 1
			],
			[
				'title'      => __('Affiliate terms & conditions'),
				'identifier' => 'affiliate-term-condition',
				'content'    => $termcontent,
				'is_active'  => 1
			],
		];
	}
}
