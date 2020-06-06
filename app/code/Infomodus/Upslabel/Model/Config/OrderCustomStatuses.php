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

class OrderCustomStatuses implements OptionSourceInterface
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

        $deprecatedStatuses = ['canceled' => 1, 'closed' => 1, 'complete' => 1, 'fraud' => 1, 'processing' => 1, 'pending' => 1];
        foreach ($orderStatusCollection as $orderStatus) {
            if (!isset($deprecatedStatuses[$orderStatus['status']])) {
                $status[] = [
                    'value' => $orderStatus['status'], 'label' => $orderStatus['label']
                ];
            }
        }

        return $status;
    }
}
