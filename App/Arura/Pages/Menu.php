<?php

namespace Arura\Pages;


use Arura\SystemLogger\SystemLogger;
use Monolog\Logger;

class Menu{

    /**
     * @var string
     */
    protected static $sFilePath = __WEB_TEMPLATES__ . 'menu.json';

    /**
     * @return array
     */
    public static function getMenuStructure(){
        if (is_file(self::$sFilePath)){
            return json_array_decode(file_get_contents(self::$sFilePath));
        }
        SystemLogger::addRecord(SystemLogger::Website, Logger::WARNING, "A menu file is not present. A new menu file is created");
        file_put_contents(self::$sFilePath, "[]");
        return [];
    }

}