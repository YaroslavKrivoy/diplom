<?php
/**
 * OrderComments : Checkout.php
 * @copyright Customer Paradigm. 2018
 * @date 2/5/18 7:57 AM
 * @version 1.0.2
 * @since 1.0.2
 */

namespace CustomerParadigm\OrderComments\Model;

/**
 * Class Checkout
 * @package CustomerParadigm\OrderComments\Model
 */
abstract class Checkout
{
    const STATUS = 'status';
    const ENTITY_ID = 'entity_id';
    const COMMENT = 'comment';
    const PARENT_ID = 'parent_id';
    const VISIBLE_ON_FRONT = 'is_visible_on_front';
    const CUSTOMER_NOTIFIED = 'is_customer_notified';
    const ENTITY_NAME = 'entity_name';
    const ORDER = 'order';
    const COMMENT_PREFIX = 'ORDER COMMENT: ';
}
