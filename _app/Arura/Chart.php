<?php
namespace Arura;


use ChartJs\ChartJS;

class Chart extends ChartJS {
    const Quarters = [
                ["Januarie", "Februari", "Maart"],
                ["April", "Mei", "Juni"],
                ["Juli", "Augustus", "Setember"],
                ["Oktober", "November", "December"]
            ];
    public static function Build($sType, $data = [], $options = [], $attributes = []){
        return (string)new self($sType,$data,$options,$attributes);
    }
}