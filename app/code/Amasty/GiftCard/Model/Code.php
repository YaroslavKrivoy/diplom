<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Magento\Framework\Model\AbstractModel;

class Code extends AbstractModel implements CodeInterface
{
    const STATE_USED = 1;
    const STATE_UNUSED = 0;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\GiftCard\Model\ResourceModel\Code');
        $this->setIdFieldName('code_id');
    }

    public function isUsed()
    {
        return $this->getUsed() == self::STATE_USED;
    }

    public function loadFreeCode($codeSetId)
    {
        $this->_getResource()->loadFreeCode($this, $codeSetId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeId()
    {
        return $this->_getData(CodeInterface::CODE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeId($codeId)
    {
        $this->setData(CodeInterface::CODE_ID, $codeId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeSetId()
    {
        return $this->_getData(CodeInterface::CODE_SET_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeSetId($codeSetId)
    {
        $this->setData(CodeInterface::CODE_SET_ID, $codeSetId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_getData(CodeInterface::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->setData(CodeInterface::CODE, $code);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsed()
    {
        return $this->_getData(CodeInterface::USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsed($used)
    {
        $this->setData(CodeInterface::USED, $used);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->_getData(CodeInterface::ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->setData(CodeInterface::ENABLED, $enabled);

        return $this;
    }
}
