<?php

namespace Arura\Settings;

use Arura\Database;
use Arura\Exceptions\Error;
use Arura\Exceptions\NotFound;
use Arura\SystemLogger\SystemLogger;

class Application{

    protected static $aSettingData = [];


    /**
     * @throws Error
     */
    protected static function load(){
        if (empty(self::$aSettingData)){
            $db = new Database();
            $aDbData = $db->fetchAll('SELECT Setting_Plg, Setting_Name, Setting_Value FROM tblSettings');

            foreach ($aDbData as $aSetting){
                self::$aSettingData[$aSetting['Setting_Plg']][$aSetting['Setting_Name']] = $aSetting['Setting_Value'];
            }
        }
    }

    /**
     * @param string $sPlg
     * @param string $sName
     * @return mixed
     * @throws Error
     */
    public static function get($sPlg = "", $sName = ""){
        self::load();
        if (!isset(self::$aSettingData[$sPlg][$sName])){
            SystemLogger::AddException(SystemLogger::Settings, new NotFound("Setting not found {$sPlg}:{$sName}", 500));
            exit;
        }
        return self::$aSettingData[$sPlg][$sName];
    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAll(){
        self::load();
        return self::$aSettingData;
    }


}