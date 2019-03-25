<?php
namespace Arura\FileManger;

class FileManger{


    public function uploadFiles(){
        foreach ($_FILES as $file){
            var_dump($file);
            if (!is_file(__FILES__ . $file['name']) && !is_dir(__FILES__ . $file['name'])){
                move_uploaded_file($file['tmp_name'], __FILES__ . $file['name']);
            } else {
                unlink($file['tmp_name']);
                throw new \Exception('file or directorie already exits',500);
            }
        }
    }


    public function loadDir($sDir = null){
        $sDirLockUp = (is_null($sDir) ? __FILES__ : __FILES__ . $sDir);

        $aOutcome = [];

        foreach (scandir($sDirLockUp) as $Item){
            $sPath = $sDirLockUp .$Item;
            $sMangerPath = $sDir . $Item;

            if (strlen($Item) > 3){
                if (is_file($sPath)){
                    $aOutcome[] =
                        [
                            'text' => $Item,
                            'dir' => $sMangerPath,
                            'icon' => self::getIcon(self::getFileType($sPath)),
                            'type' => self::getFileType($sPath)
                        ];
                } else if (is_dir($sPath)){
                    $isDirEmpty = (new \FilesystemIterator($sPath))->valid();
                    $aOutcome[] =
                        [
                            'text' => $Item,
                            'children' => $isDirEmpty,
                            'dir' => $sMangerPath . '/', 'icon' => self::getIcon(),
                            'type' => self::getFileType($sPath)
                        ];
                }
            }

        }

        return $aOutcome;
    }

    public static function getIcon($sFileType = ''){
        $sType = '';
        switch ($sFileType){
            case 'file':
                $sType = 'fas fa-file';
                break;
            case 'img':
                $sType = 'fas fa-file-image';
                break;
            default:
                $sType = 'fas fa-folder';
                break;
        }
        return $sType;
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

    public function deleteItem($sPath){
        $sPath = __FILES__ . $sPath;
        if (is_file($sPath)){
            unlink($sPath);
        } else if (is_dir($sPath)){
            rmdir($sPath);
        } else {
            throw new \Exception('item does not exists', 500);
        }
        return true;
    }

}