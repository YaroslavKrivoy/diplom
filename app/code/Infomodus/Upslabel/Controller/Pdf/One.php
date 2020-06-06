<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Controller\Pdf;

class One extends \Infomodus\Upslabel\Controller\Pdf
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
            } else {
                $this->resultRedirectFactory->create()->setUrl($this->_buildUrl('*/*/'));
            }
        } else {
            $this->resultRedirectFactory->create()->setUrl($this->_buildUrl('*/*/'));
        }
    }
}
