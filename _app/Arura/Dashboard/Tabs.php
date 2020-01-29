<?php
namespace Arura\Dashboard;

use Arura\Exceptions\BadRequest;

class Tabs{

    /**
     * @var array
     */
    private $aTabs = [];
    /**
     * @var string
     */
    private $sHeader = "";

    /**
     * @param string $para
     * @param string $sLocation
     * @param callable|null $Function
     * @param null $iRight
     */
    public function addTab($para = "", $sLocation = "", callable $Function = null, $iRight = null){
        $this->aTabs[$para] = [
            "Html" => $sLocation,
            "Right" => $iRight,
            "Function" => $Function
        ];
    }

    /**
     * @param string $sLocation
     */
    public function setHeader($sLocation = ""){
        $this->sHeader = Page::getHtml($sLocation);
    }

    /**
     * @param string $sPara
     * @return string
     * @throws BadRequest
     */
    public  function getPage($sPara = ""){
        if (isset($this->aTabs[$sPara])){
            if (!is_null($this->aTabs[$sPara]["Function"])){
                $this->aTabs[$sPara]["Function"]();
            }
            return ($this->sHeader . Page::getHtml($this->aTabs[$sPara]["Html"]));
        } else {
            throw new BadRequest();
        }
    }
}