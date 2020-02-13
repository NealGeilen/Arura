<?php
namespace Arura\Pages\CMS;

use Arura\Database;
use Arura\Exceptions\Error;

class Addon {

    /**
     * @param $iAddonId
     * @return mixed
     * @throws Error
     */
    public static function getAddon($iAddonId){
        $db = new Database();
        return $db->fetchRow('SELECT * FROM tblCmsAddons WHERE Addon_Id = ? ',
            [
                $iAddonId
            ]);
    }

    /**
     * @return array
     * @throws Error
     */
    public static function getAllAddons(){
        $db = new Database();
        $aAddons = $db->fetchAll('SELECT * FROM tblCmsAddons WHERE Addon_Active = 1');
        $aList = [];
        foreach ($aAddons as $ikey =>$aAddon){
            $aList[(int)$aAddon['Addon_Id']] = $aAddon;
            $aList[(int)$aAddon['Addon_Id']]['AddonSettings'] = $db->fetchAll('SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = ? ORDER BY AddonSetting_Position ASC',
                [
                    (int)$aAddon['Addon_Id']
                ]);
        }
        return$aList;
    }


}