<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 20.09.19
 * Time: 17:21
 */

namespace KozakGroup\RewriteOrderEditor\Plugin\Model\Order;

use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Framework\Mail\MessageInterface;

class ClearFromMessagePlugin
{

    protected $message;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }

    public function beforeSetFromByStore(TransportBuilderByStore $subject, $from, $store)
    {
        $this->message->clearFrom();
    }

}