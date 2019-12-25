<?php
namespace Arura\Dashboard;

use Arura\Exceptions\BadRequest;

class Tabs{

    private $aTabs = [];
    private $sHeader = "";

    public function addTab($para, $sLocation,callable $Function = null, $iRight = null){
        $this->aTabs[$para] = [
            "Html" => $sLocation,
            "Right" => $iRight,
            "Function" => $Function
        ];
    }

    public function setHeader($sLocation){
        $this->sHeader = Page::getHtml($sLocation);
    }

    public  function getPage($sPara){
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