<?php
namespace Arura\FileManger;

class FileManger{

    public static $FileMangerRight;


    public function uploadFiles(){
    }


    public function loadDir($sDir = null){
        $sDirLockUp = (is_null($sDir) ? __FILES__ : __FILES__ . $sDir);

        $aOutcome = [];

        foreach (scandir($sDirLockUp) as $Item){
            $sPath = $sDirLockUp .$Item;
            $sMangerPath = $sDir . $Item;

            if (strlen($Item) > 3){
                if (is_file($sPath)){
                    $aOutcome[] = ['text' => $Item, 'dir' => $sMangerPath, 'type' => self::getFileType($sPath)];
                } else if (is_dir($sPath)){
                    $isDirEmpty = !(new \FilesystemIterator($sPath))->valid();
                    $aOutcome[] = ['text' => $Item, 'children' => $isDirEmpty, 'dir' => $sMangerPath];
                }
            }

        }

        return $aOutcome;
    }

    public static function getFileType($sFilePath){
        if (is_file($sFilePath)){
            $sType = '';
            switch (strtolower(pathinfo($sFilePath, PATHINFO_EXTENSION))){
                case 'png':
                case 'jpg':
                case 'gif':
                    $sType = 'img';
                    break;
                default:
                    $sType = 'file';
                    break;
            }
            return $sType;
        }
        return false;
    }

}