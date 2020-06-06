<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface CodeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CODE_ID = 'code_id';
    const CODE_SET_ID = 'code_set_id';
    const CODE = 'code';
    const USED = 'used';
    const ENABLED = 'enabled';
    /**#@-*/

    /**
     * @return int
     */
    public function getCodeId();

    /**
     * @param int $codeId
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function setCodeId($codeId);

    /**
     * @return int|null
     */
    public function getCodeSetId();

    /**
     * @param int|null $codeSetId
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function setCodeSetId($codeSetId);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function setCode($code);

    /**
     * @return int
     */
    public function getUsed();

    /**
     * @param int $used
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function setUsed($used);

    /**
     * @return int
     */
    public function getEnabled();

    /**
     * @param int $enabled
     *
     * @return \Amasty\GiftCard\Api\Data\CodeInterface
     */
    public function setEnabled($enabled);
}
