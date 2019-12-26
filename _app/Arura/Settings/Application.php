<?php

namespace Arura\Settings;

use Arura\Database;

class Application{

    protected static $aSettingData = [];


    protected static function load(){
        if (empty(self::$aSettingData)){
            $db = new Database();
            $aDbData = $db->fetchAll('SELECT Setting_Plg, Setting_Name, Setting_Value FROM tblSettings');

            foreach ($aDbData as $aSetting){
                self::$aSettingData[$aSetting['Setting_Plg']][$aSetting['Setting_Name']] = $aSetting['Setting_Value'];
            }
        }
    }

    public static function get($sPlg, $sName){
        self::load();
        return self::$aSettingData[$sPlg][$sName];
    }

    public static function getAll(){
        self::load();
        return self::$aSettingData;
    }


}