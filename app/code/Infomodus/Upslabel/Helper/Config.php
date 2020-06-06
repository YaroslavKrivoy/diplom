<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */

namespace Infomodus\Upslabel\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    public $error = true;
    public $testing = false;
    protected $filesystem;
    protected $storeManager;
    private $ch;

    public function __construct(
        Context $context,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    )
    {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function log($error, array $context = [])
    {
        $this->_logger->error($error, $context);
    }

    public function getBaseDir($alias)
    {
        return $this->filesystem->getDirectoryRead($alias)->getAbsolutePath();
    }

    public function getBaseUrl($alias)
    {
        return $this->storeManager->getStore()->getBaseUrl($alias);
    }

    public function getStoreConfig($configPath, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    public function createMediaFolders()
    {
        $baseMediaDir = $this->getBaseDir('media');

        $path_upsdir = $baseMediaDir . '/upslabel';
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
        }
        $path_upsdir = $baseMediaDir . '/upslabel/label';
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
        }
        $path_upsdir = $baseMediaDir . '/upslabel/test_xml';
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
        }

        if (is_dir($path_upsdir)) {
            if (!file_exists($path_upsdir . "/.htaccess")) {
                file_put_contents($path_upsdir . "/.htaccess", "deny from all");
            }
        }
    }

    public function getRequest()
    {
        return $this->_getRequest();
    }

    public function getUrl($route, $params = [])
    {
        return parent::_getUrl($route, $params);
    }

    public function escapeXML($string)
    {
        $string = preg_replace('/&/is', '&amp;', $string);
        $string = preg_replace('/</is', '&lt;', $string);
        $string = preg_replace('/>/is', '&gt;', $string);
        $string = preg_replace('/\'/is', '&#39;', $string);
        $string = preg_replace('/"/is', '&quot;', $string);
        $string = str_replace(
            [
                'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż', 'ü', 'ò', 'è',
                'à', 'ì', 'é', 'ô', 'Ä', 'ä', 'Ü', 'ü', 'Ö', 'ö', 'ß',
                'À', 'Á', 'Â', 'Ã', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ô', 'Õ', 'Ù',
                'Ú', 'Û', 'Ý', 'Þ', 'á', 'â', 'ã', 'å', 'æ', 'ç', 'ê',
                'ë', 'í', 'î', 'ï', 'ð', 'ñ', 'õ', 'ù', 'ú', 'û', 'ý', 'þ', 'ÿ', 'Œ', 'œ', 'Š', 'š', 'Ÿ',
                'Ø', 'ø',
            ],
            [
                '&#261;', '&#263;', '&#281;', '&#322;', '&#324;', '&#243;', '&#347;', '&#378;', '&#380;', '&#260;',
                '&#262;', '&#280;', '&#321;', '&#323;', '&#211;', '&#346;',
                '&#377;', '&#379;', '&#252;', '&#242;', '&#232;', '&#224;', '&#236;', '&#233;', '&#244;', '&#196;',
                '&#228;', '&#220;', '&#252;', '&#214;', '&#246;', '&#223;',
                '&#192;', '&#193;', '&#194;', '&#195;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;',
                '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;',
                '&#209;', '&#210;', '&#212;', '&#213;', '&#217;', '&#218;', '&#219;', '&#221;', '&#222;', '&#225;',
                '&#226;', '&#227;', '&#229;', '&#230;', '&#231;', '&#234;',
                '&#235;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#245;', '&#249;', '&#250;', '&#251;',
                '&#253;', '&#254;', '&#255;', '&#338;', '&#339;', '&#352;',
                '&#353;', '&#376;', '&#216;', '&#248;',
            ],
            $string
        );

        return $string;
    }

    public function escapePhone($phone)
    {
        return str_replace([" ", "+", "-"], ["", "", ""], $phone);
    }

    public function curlSend($url, $data = null)
    {
        $this->error = true;
        $result = $this->curlSetOption($url, $data);
        $ch = $this->ch;
        if ($result) {
            $result1 = $result;
            $result = strstr($result, '<?xml');
            if ($result === false) {
                $result = $result1;
            }
            curl_close($ch);
            $this->error = false;
            return $result;
        } else {
            $this->error = true;
            $data = ['errordesc' => 'Server Error (cUrl)', 'error' => curl_errno($ch) . ' - ' . curl_error($ch)];
            curl_close($ch);
            return $data;
        }
    }

    public function curlSetOption($url, $data = null)
    {
        /*$sslV = curl_version();*/
        $ch = curl_init($url);
        /*if ($data != null) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        } else {*/
        curl_setopt($ch, CURLOPT_HEADER, 0);
        /*}*/

        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->testing);
        /*curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);*/
        /*if (strpos($sslV['ssl_version'], 'NSS/') === false) {
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
        }*/
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_ENCODING, "");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Cookie: WEMEnabled=Y'
            ]);
        }
        $this->ch = $ch;

        return curl_exec($ch);
    }

    public function sendPrint($datas, $storeId = null)
    {
        try {
            $ip = trim($this->getStoreConfig('upslabel/printing/automatic_printing_ip', $storeId));
            $port = trim($this->getStoreConfig('upslabel/printing/automatic_printing_port', $storeId));
            if ($ip != '' && $port != '') {
                if (!is_array($datas)) {
                    $datas = array($datas);
                }

                foreach ($datas as $data) {
                    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

                    if ($socket === false) {
                        $this->_logger->error("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
                    } else {
                        $result = socket_connect($socket, $ip, $port);
                        if ($result === false) {
                            $this->_logger->error("socket_connect() failed.\nReason: (" . $ip . ":" . $port . ") "
                                . socket_strerror(socket_last_error($socket)));
                        } else {
                            socket_write($socket, $data, strlen($data));
                        }
                    }
                    socket_close($socket);
                }
            } else {
                $this->_logger->error("ip and port are not specified");
            }
        } catch (\Exception $e) {
            $this->_logger->error("Print error " . $e->getCode() . ": " . $e->getMessage());
        }
    }

    public
    function getStoreByCode($storeCode)
    {
        $stores = array_keys($this->storeManager->getStores());
        foreach ($stores as $id) {
            $store = $this->storeManager->getStore($id);
            if ($store->getCode() == $storeCode) {
                return $store;
            }
        }
        return null;
    }

    public
    function getUpsCode($code)
    {
        $codes = [
            '1DM' => '14',
            '1DA' => '01',
            '1DP' => '13',
            '2DM' => '59',
            '2DA' => '02',
            '3DS' => '12',
            'GND' => '03',
            'EP' => '54',
            'XDM' => '54',
            'XPD' => '08',
            'XPR' => '07',
            'ES' => '07',
            'SV' => '65',
            'EX' => '08',
            'ST' => '11',
            'ND' => '07',
            'WXS' => '65',
        ];
        return isset($codes[$code]) ? $codes[$code] : null;
    }

    public
    function getStrMaxLength($str, $len)
    {
        $str = $this->escapeXML($str);
        if (strlen($str) > $len) {
            return preg_replace('/\s+?(\S+)?$/', '', substr($str, 0, $len + 1));
        }
        return $str;
    }
}
