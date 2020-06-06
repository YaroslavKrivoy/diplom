<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\CustomerStatistic;

use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\Autorelated\Model\CustomerStatistic
 */
class Manager
{
    /**
     * Lifetime for actions (in seconds)
     */
    const LIFETIME_ACTION = 0;

    /**#@+
     * Prefixes for actions names
     */
    const VIEW_ACTION_NAME_PREFIX = 'arp_view_';
    const CLICK_ACTION_NAME_PREFIX = 'arp_click_';
    /**#@-*/

    /**
     * @var array
     */
    private $actionsArray;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        SessionManagerInterface $sessionManager
    ) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Check if need to update views statistic for rule
     *
     * @param int $ruleId
     * @return bool
     */
    public function isNeedToUpdateViewsStatisticForRule($ruleId)
    {
        $viewActionName = $this->getViewActionNameForRule($ruleId);
        return $this->isNeedToUpdateStatisticForAction($viewActionName);
    }

    /**
     * Check if need to update clicks statistic for rule
     *
     * @param int $ruleId
     * @return bool
     */
    public function isNeedToUpdateClicksStatisticForRule($ruleId)
    {
        $clickActionName = $this->getClickActionNameForRule($ruleId);
        return $this->isNeedToUpdateStatisticForAction($clickActionName);
    }

    /**
     * Retrieve view action name for rule
     *
     * @param int $ruleId
     * @return string
     */
    private function getViewActionNameForRule($ruleId)
    {
        return self::VIEW_ACTION_NAME_PREFIX . $ruleId;
    }

    /**
     * Retrieve click action name for rule
     *
     * @param int $ruleId
     * @return string
     */
    private function getClickActionNameForRule($ruleId)
    {
        return self::CLICK_ACTION_NAME_PREFIX . $ruleId;
    }

    /**
     * Check if need to update statistic for action with specified name
     *
     * @param string $actionName
     * @return bool
     */
    private function isNeedToUpdateStatisticForAction($actionName)
    {
        $result = true;
        if ($this->isNeedToUseSession()) {
            return $this->checkAndRenewIfNeedDataInSessionForAction($actionName);
        }
        return $result;
    }

    /**
     * Check if need to use saving info about actions in session to prevent frequent statistic update
     *
     * @return bool
     */
    private function isNeedToUseSession()
    {
        return self::LIFETIME_ACTION > 0;
    }

    /**
     * Check if record about action exist in customer session and if need - add record about action
     *
     * @param string $actionName
     * @return bool
     */
    private function checkAndRenewIfNeedDataInSessionForAction($actionName)
    {
        if ($this->isSetAction($actionName)) {
            $result = false;
        } else {
            $result = true;
            $this->addAction($actionName);
        }
        return $result;
    }

    /**
     * Get all arp actions from session
     *
     * @return array
     */
    private function getActions()
    {
        if (null === $this->actionsArray) {
            $this->actionsArray = $this->sessionManager->getData();

            if (is_array($this->actionsArray) && count($this->actionsArray)) {
                // Check and remove old actions from array
                foreach ($this->actionsArray as $key => $expireTime) {
                    if ($this->isDataRelatesToArp($key)) {
                        if ($expireTime <= time()) {
                            unset($this->actionsArray[$key]);
                        }
                    }
                }
            } else {
                $this->actionsArray = [];
            }
        }

        return $this->actionsArray;
    }

    /**
     * Check if session data relates to arp
     *
     * @param mixed $key
     * @return bool
     */
    private function isDataRelatesToArp($key)
    {
        return (is_string($key))
            && (
                (strpos($key, self::VIEW_ACTION_NAME_PREFIX) !== false)
                || (strpos($key, self::CLICK_ACTION_NAME_PREFIX) !== false)
            )
        ;
    }

    /**
     * Is set actionName in session
     *
     * @param string $actionName
     * @return bool
     */
    public function isSetAction($actionName)
    {
        $actionsArray = $this->getActions();
        if (is_array($actionsArray) && isset($actionsArray[$actionName])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add $actionName and expire time to array
     *
     * @param string $actionName
     * @return $this
     */
    public function addAction($actionName)
    {
        $this->actionsArray[$actionName] = self::LIFETIME_ACTION + time();
        // Save data in session
        $this->sessionManager->setData($this->actionsArray);

        return $this;
    }
}
