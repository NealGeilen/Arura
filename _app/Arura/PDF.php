<?php
namespace Arura;
use Arura\Exceptions\Error;
use Mpdf\Mpdf;

class PDF extends Mpdf {

    protected $template;
    protected $oSmarty;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->oSmarty = new \Smarty();
    }

    public function setTemplate($sFile){
        if (is_file($sFile)){
            $this->template = $sFile;
        }
    }

    public function assign($sName, $sValue){
        $this->oSmarty->assign($sName,$sValue);
    }

    public function Output($name = '', $dest = '')
    {
        if (is_file($this->template)){
            $this->WriteHTML($this->oSmarty->fetch($this->template));
        } else {
            throw new Error("Template file not valid");
        }
        return parent::Output($name, $dest);
    }
}