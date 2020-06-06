<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pdflabels;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Many extends AbstractMassAction
{
    protected $labelCollection;
    protected $fileFactory;

    protected function massAction(AbstractCollection $collection)
    {
        $ids = $collection->getAllIds();
        if (count($ids) > 0) {
            $labels = $this->labelCollection
                ->addFieldToFilter('lstatus', 0);
            if ($this->conf->getStoreConfig('upslabel/printing/bulk_printing_all') == 1) {
                $labels->addFieldToFilter('rva_printed', 0);
            }

            $paramName = $this->getRequest()->getParam('namespace', null);
            if ($paramName === null) {
                $paramName = $this->getRequest()->getParam('massaction_prepare_key', null);
            }

            switch ($paramName) {
                case 'sales_order_grid':
                    $labels->addFieldToFilter('order_id', ['in' => $ids]);
                    $errorLink = 'sales/order';
                    break;
                case 'sales_order_shipment_grid':
                    $labels->addFieldToFilter('shipment_id', ['in' => $ids])
                        ->addFieldToFilter('type', [['like'=>'shipment'],['like'=>'invert']]);
                    $errorLink = 'sales/shipment';
                    break;
                case 'sales_order_creditmemo_grid':
                    $labels->addFieldToFilter('shipment_id', ['in' => $ids])->addFieldToFilter('type', 'refund');
                    $errorLink = 'sales/creditmemo';
                    break;
                default:
                    $labels->addFieldToFilter('upslabel_id', ['in' => $ids]);
                    $errorLink = 'infomodus_upslabel/items';
                    break;
            }

            $labels->load();
            if (count($labels) > 0) {
                $data = $this->pdf->createManyPDF($labels);
                if ($data !== false && count($data->pages) > 0) {
                    return $this->fileFactory->create(
                        'ups_shipping_labels.pdf',
                        $data->render(),
                        \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                } else {
                    $this->messageManager->addWarningMessage(__('For the selected items are not created PDF labels.'));
                    return $this->resultRedirectFactory->create()->setPath($errorLink.'/');
                }
            } else {
                $this->messageManager->addErrorMessage(__('For the selected items are not created labels.'));
                return $this->resultRedirectFactory->create()->setPath($errorLink.'/');
            }
        }
    }
}
