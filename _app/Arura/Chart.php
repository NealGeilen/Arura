<?php
namespace Arura;


use ChartJs\ChartJS;

class Chart extends ChartJS {
    const Quarters = [
                [
                    0 => "Januarie",
                    1=> "Februari",
                    2=>"Maart"
                ],
                [
                    3=>"April",
                    4=>"Mei",
                    5=>"Juni"
                ],
                [
                    6=>"Juli",
                    7=>"Augustus",
                    8=> "Setember"
                ],
                [
                    9=>"Oktober",
                    10=>"November",
                    11=>"December"]
            ];
    public static function Build($sType, $data = [], $options = [], $attributes = []){
        return (string)new self($sType,$data,$options,$attributes);
    }
}