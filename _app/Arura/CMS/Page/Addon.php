<?php
namespace Arura\CMS\Page;

use NG\Database;

class Addon{

    protected $oDatabase;


    public function __construct(){
        $this -> oDatabase = new Database();
    }

    public function getAddonSettings($iAddonId){
        return $this->oDatabase->fetchAll('SELECT * FROM tblCmsAddonSettings WHERE AddonSetting_Addon_Id = ? ORDER BY AddonSetting_Position ASC',
            [
                (int)$iAddonId
            ]);
    }


    public function getAllAddons(){
        $aAddons = $this->oDatabase->fetchAll('SELECT * FROM tblCmsAddons WHERE Addon_Active = 1');
        $aList = [];
        foreach ($aAddons as $ikey =>$aAddon){
            $aList[(int)$aAddon['Addon_Id']] = $aAddon;
            $aList[(int)$aAddon['Addon_Id']]['AddonSettings'] = $this -> getAddonSettings($aAddon['Addon_Id']);
        }
        return$aList;
    }


}