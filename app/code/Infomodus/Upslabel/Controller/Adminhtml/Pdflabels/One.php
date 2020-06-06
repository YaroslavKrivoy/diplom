<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pdflabels;

class One extends \Infomodus\Upslabel\Controller\Adminhtml\Pdflabels
{
    public function execute()
    {
        $labelId = $this->getRequest()->getParam('label_id', null);
        if ($labelId !== null) {
            $data = $this->pdf->createPDF($labelId);
            if ($data !== false) {
                return $this->fileFactory->create(
                    'ups_shipping_labels.pdf',
                    $data->render(),
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        }
    }
}
