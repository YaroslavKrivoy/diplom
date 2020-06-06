<?php
/**
 * OrderComments : Handler.php
 * @copyright CustomerParadigm 2018
 * @date 2/15/18 1:53 PM
 * @version 1.0.2
 * @since 1.0.2
 */

namespace CustomerParadigm\OrderComments\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package CustomerParadigm\OrderComments\Logger
 */
class Handler extends Base
{
    protected $loggerType = Logger::INFO;
    protected $fileName = '/var/log/ordercomments.log';
}
