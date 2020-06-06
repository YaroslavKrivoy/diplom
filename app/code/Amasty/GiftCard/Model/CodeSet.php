<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\CodeSetInterface;
use Magento\Framework\Model\AbstractModel;

class CodeSet extends AbstractModel implements CodeSetInterface
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\GiftCard\Model\ResourceModel\CodeSet');
        $this->setIdFieldName('code_set_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeSetId()
    {
        return $this->_getData(CodeSetInterface::CODE_SET_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeSetId($codeSetId)
    {
        $this->setData(CodeSetInterface::CODE_SET_ID, $codeSetId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_getData(CodeSetInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(CodeSetInterface::TITLE, $title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->_getData(CodeSetInterface::TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $this->setData(CodeSetInterface::TEMPLATE, $template);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->_getData(CodeSetInterface::ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->setData(CodeSetInterface::ENABLED, $enabled);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        return $this->_getData(CodeSetInterface::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditions($conditions)
    {
        $this->setData(CodeSetInterface::CONDITIONS_SERIALIZED, $conditions);
    }
}
