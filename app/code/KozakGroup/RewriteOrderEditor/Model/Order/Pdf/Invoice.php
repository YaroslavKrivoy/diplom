<?php

namespace KozakGroup\RewriteOrderEditor\Model\Order\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    public function _customDrawHeader(\Zend_Pdf_Page $page, $y)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $this->y = $y;
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price'), 'feed' => 355, 'align' => 'right'];

        $lines[0][] = ['text' => __('Wgt'), 'feed' => 395, 'align' => 'right'];

        $lines[0][] = ['text' => __('Qty/Cases'), 'feed' => 445, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price/lb'), 'feed' => 485, 'align' => 'right'];

        $lines[0][] = ['text' => __('Lb.'), 'feed' => 515, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
}
