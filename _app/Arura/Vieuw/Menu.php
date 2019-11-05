<?php

namespace AruraSite;


class  Menu{

    protected static $sFilePath = __TEMPLATES__ . 'menu.json';

    public static function getMenuStructure(){
        return json_array_decode(file_get_contents(self::$sFilePath));
    }

}