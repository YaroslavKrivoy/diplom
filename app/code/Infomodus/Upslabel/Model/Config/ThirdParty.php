<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Infomodus\Upslabel\Model\Account;
use Magento\Framework\Data\OptionSourceInterface;

class ThirdParty implements OptionSourceInterface
{
    protected $account;

    public function __construct(
        Account $account
    )
    {
        $this->account = $account;
    }

    public function toOptionArray()
    {
        $c = [['value'=>0, 'label'=> __("Shipper")]];
        $upsAcctModel = $this->account->getCollection();
        foreach ($upsAcctModel as $account) {
            $c[] = ['value' => $account->getId(), 'label' => $account->getCompanyname()];
        }
        return $c;
    }
}
