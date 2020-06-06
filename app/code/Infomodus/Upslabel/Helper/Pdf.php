<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
namespace Infomodus\Upslabel\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Pdf extends AbstractHelper
{
    protected $_conf;
    private $labels;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Infomodus\Upslabel\Model\Items $labels,
        \Infomodus\Upslabel\Helper\Config $conf
    ) {
        $this->labels = $labels;
        $this->_conf = $conf;
        parent::__construct($context);
    }

    public function createManyPDF($labels)
    {
        $pdf = null;
        foreach ($labels as $label) {
            $pdf = $this->createPDF($label, $pdf);
        }

        $labelFirst = $labels->getFirstItem();
        if ($labelFirst->getInternationalInvoice() == 1 && $this->_conf->getStoreConfig('upslabel/paperless/print_pdf', $labelFirst->getStoreId()) == 0) {
            $pathInvoice = $this->_conf->getBaseDir('media') . '/upslabel/inter_pdf/';
            if (file_exists($pathInvoice . $labelFirst->getShipmentidentificationnumber() . ".pdf")) {
                $pdf2 = \Zend_Pdf::load($pathInvoice . $labelFirst->getShipmentidentificationnumber() . ".pdf");
                foreach ($pdf2->pages as $k => $page) {
                    $template2 = clone $pdf2->pages[$k];
                    $page2 = new \Zend_Pdf_Page($template2);
                    $pdf->pages[] = $page2;
                }
            }
        }

        return $pdf;
    }

    public function createPDF($labelId, $pdf = null)
    {
        $imgPath = $this->_conf->getBaseDir('media') . '/upslabel/label/';
        if ($pdf === null) {
            $pdf = new \Zend_Pdf();
            $pdf->properties['Title'] = 'UPS shipping labels';
            $pdf->properties['Author'] = 'Infomodus';
            $pdf->properties['Producer'] = 'Zend PDF';
            $pdf->properties['Creator'] = 'Zend PDF';
            $pdf->properties['CreationDate'] = 'D:' . date('YmdHis', time());
            $pdf->properties['ModDate'] = 'D:' . date('YmdHis', time());
        }

        if (is_string($labelId)) {
            $label = $this->labels->load($labelId);
        } else {
            $label = $labelId;
        }

        if ($label
            && file_exists($imgPath . $label->getLabelname())
            && filesize($imgPath . $label->getLabelname()) > 256
        ) {
            if ($label->getTypePrint() == "GIF") {
                $pdf->pages[] = $this->_setLabelToPage($imgPath . $label->getLabelname());
            } else {
                $ip = trim($this->_conf->getStoreConfig('upslabel/printing/automatic_printing_ip', $label->getStoreId()));
                $port = trim($this->_conf->getStoreConfig('upslabel/printing/automatic_printing_port', $label->getStoreId()));
                if (strlen($ip) > 0 && strlen($port) > 0 && $this->_conf->getStoreConfig('upslabel/printing/printer', $label->getStoreId()) != 'GIF') {
                    $data = file_get_contents($imgPath . $label->getLabelname());
                    $this->_conf->sendPrint($data, $label->getStoreId());
                }
            }

            $label->setRvaPrinted(1);
            $label->save();
        }

        return $pdf;
    }

    private function _setLabelToPage($label)
    {
        $image = imagecreatefromstring(file_get_contents($label));

        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        if ($this->_conf->getStoreConfig('upslabel/printing/papersize') != "AC") {
            if ($this->_conf->getStoreConfig('upslabel/printing/papersize') == "A4") {
                if ($xSize > 595) {
                    $ySize = $ySize * (595 / $xSize);
                    $xSize = 595;
                }

                $page = new \Zend_Pdf_Page(\Zend_Pdf_Page::SIZE_A4);
            } else {
                $page = new \Zend_Pdf_Page($xSize, $ySize);
            }
        } else {
            $ySize = $this->_conf->getStoreConfig('upslabel/printing/custom_width') * ($ySize / $xSize);
            $xSize = $this->_conf->getStoreConfig('upslabel/printing/custom_width');
            $page = new \Zend_Pdf_Page($xSize, $ySize);
        }

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . '/lbl' . rand(10000, 999999) . '.png';
        imagepng($image, $tmpFileName);
        $image = \Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($image, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return ($page);
    }
}
