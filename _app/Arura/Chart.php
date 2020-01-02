<?php
namespace Arura;

use Arura\Dashboard\Page;
use Arura\Exceptions\Error;

class Chart{

    protected $aData;
    protected $sCssId;
    protected $sType = "bar";

    public function __construct($sType = "bar")
    {
        $this->sType = $sType;
        $this->sCssId=str_random();
    }

    public function addDataSet($sSql, $Parameters = [], $sRgbaColor = ""){
        $db = new Database();
        $qData = $db->fetchAll($sSql,$Parameters);
        $aDataSet = [];

        $aDataSet["backgroundColor"] = "rgba(".$sRgbaColor.")";
        $aDataSet["borderColor"] = "rgba(".$sRgbaColor.")";
        foreach ($qData as $aRecord){
            if (isset($aRecord["x"]) && isset($aRecord["y"])){
                $aDataSet["data"][] = $aRecord;
            } else {
                throw new Error("X or Y not set");
            }
            if (isset($aRecord["name"])){
                $aDataSet["label"] = $aRecord["name"];
            }
        }
        $this->aData[] = $aDataSet;
    }


    public function setDataWithQuery($sql, $parameters = []){

    }

    public function __toString()
    {
        return "<canvas id='".$this->sCssId."'></canvas>";
    }


    public function draw(){
        Page::addSourceScriptJs(/** @lang JavaScript */ "
        new Chart($('#".$this->sCssId."'), {
            type: '".$this->sType."',
            data: {
                datasets: ".json_encode($this->aData)."
            },
            options: {
                scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 10,
                        stepSize: 1,
                        beginAtZero: true
                    }
                }]
            }
    }
        });
        ");
    }
}