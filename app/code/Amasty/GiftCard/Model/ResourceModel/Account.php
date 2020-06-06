<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Account extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('amasty_amgiftcard_account', 'account_id');
    }

    /**
     * @param \Amasty\GiftCard\Model\Account $object
     * @param string $code
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCode(\Amasty\GiftCard\Model\Account $object, $code)
    {
        $connection = $this->getConnection();
        $query = $connection->select()
            ->from($this->getMainTable())
            ->join(
                array('code' => $this->getTable('amasty_amgiftcard_code')),
                'code.code_id = '.$this->getMainTable().'.code_id'
            )
            ->where('code.code = ?', $code)
            ->limit(1);

        $data = $connection->fetchRow($query);
        if ($data) {
            $object->setData($data);
        }

        return $this;
    }
}
