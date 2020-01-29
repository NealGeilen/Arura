<?php

namespace Arura\Pages;


class Menu{

    protected static $sFilePath = __WEB_TEMPLATES__ . 'menu.json';

    /**
     * @return array
     */
    public static function getMenuStructure(){
        return json_array_decode(file_get_contents(self::$sFilePath));
    }

}