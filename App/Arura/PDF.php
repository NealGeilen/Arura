<?php
namespace Arura;
use Arura\Exceptions\Error;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Smarty;
use SmartyException;

class PDF extends Mpdf {

    protected $template;
    protected $oSmarty;

    /**
     * PDF constructor.
     * @param array $config
     * @throws MpdfException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->oSmarty = new Smarty();
    }

    /**
     * @param string $sFile
     * @throws Error
     * @throws MpdfException
     * @throws SmartyException
     */
    public function setTemplate($sFile = ""){
        if (is_file($sFile)){
            $this->WriteHTML($this->oSmarty->fetch($sFile));
        } else {
            throw new Error("Template file not valid");
        }
    }

    /**
     * @param string $sName
     * @param string $sValue
     */
    public function assign($sName = "", $sValue = ""){
        $this->oSmarty->assign($sName,$sValue);
    }

    /**
     * @param string $header
     * @param string $OE
     * @param bool $write
     * @throws SmartyException
     */
    public function SetHTMLHeader($header = '', $OE = '', $write = false)
    {
        return parent::SetHTMLHeader($this->oSmarty->fetch($header), $OE, $write);
    }

    /**
     * @param string $footer
     * @param string $OE
     * @return bool|void
     * @throws SmartyException
     */
    public function SetHTMLFooter($footer = '', $OE = '')
    {
        return parent::SetHTMLFooter($this->oSmarty->fetch($footer), $OE);
    }

    /**
     * @param string $name
     * @param string $dest
     * @return string
     * @throws MpdfException
     */
    public function Output($name = '', $dest = '')
    {
        return parent::Output($name, $dest);
    }
}