<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */

namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\Order\Status;

class OrderStatuses implements OptionSourceInterface
{
    protected $status;
    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    public function toOptionArray($isMultiSelect = false)
    {
        $orderStatusCollection = $this->status->getResourceCollection()->getData();
        $status = [
            ['value' => "", 'label' => __('--Please Select--')]
        ];
        foreach ($orderStatusCollection as $orderStatus) {
            $status[] = [
                'value' => $orderStatus['status'], 'label' => $orderStatus['label']
            ];
        }

        return $status;
    }
}
