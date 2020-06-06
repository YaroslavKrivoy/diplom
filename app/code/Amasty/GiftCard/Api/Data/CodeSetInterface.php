<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface CodeSetInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CODE_SET_ID = 'code_set_id';
    const TITLE = 'title';
    const TEMPLATE = 'template';
    const ENABLED = 'enabled';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    /**#@-*/

    /**
     * @return int
     */
    public function getCodeSetId();

    /**
     * @param int $codeSetId
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function setCodeSetId($codeSetId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function setTemplate($template);

    /**
     * @return int
     */
    public function getEnabled();

    /**
     * @param int $enabled
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function setEnabled($enabled);

    /**
     * @return string
     */
    public function getConditions();

    /**
     * @param string $conditions
     *
     * @return \Amasty\GiftCard\Api\Data\CodeSetInterface
     */
    public function setConditions($conditions);
}
