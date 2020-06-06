<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Plugin\Catalog\Model\Product\Option\Value;

/**
 * Plugin Class
 */
class Plugin
{
    /**
     * On product custom option value save, reflect the saved ID back to the values store.
     *
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @param $result
     * @return mixed
     */
    public function afterSave(
        \Magento\Catalog\Model\Product\Option\Value $value,
        \Magento\Catalog\Model\Product\Option\Value $result
    ) {
        $newId = $result->getId();
        $values = $result->getValues();

        // Assuming any existing values with IDs will always be before new ones in the array
        // Assuming that values will always be saved in exactly the order they're on values as (true as of 2.2.1)
        if (is_array($values)) {
            foreach ($values as $k => $row) {
                if (!isset($row['option_type_id']) || $row['option_type_id'] == $newId) {
                    $values[$k] = $result->getData();
                    $result->setValues($values);
                    break;
                }
            }
        }

        return $result;
    }
}
