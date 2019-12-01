<?php
namespace Arura\FileManger;

use NG\Exceptions\NotAcceptable;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class FileManger{


    public function uploadFiles($sDir){
        foreach ($_FILES as $file){
            $sPath = __FILES__ . $sDir . $file['name'];
            if (!is_file($sPath) && !is_dir($sPath)){
                move_uploaded_file($file['tmp_name'], $sPath);
                if(exif_imagetype($sPath)) {
                    $optimizerChain = OptimizerChainFactory::create();
                    $optimizerChain->optimize((string)$sPath);
                }
            } else {
                unlink($file['tmp_name']);
                throw new \Exception('file or directorie already exits',500);
            }
        }
    }


    public function loadDir($sDir = null, $sType =null){
        $sDirLockUp = (is_null($sDir) ? __FILES__ : __FILES__ . $sDir);

        $aOutcome = [];

        foreach (scandir($sDirLockUp) as $Item){
            $sPath = $sDirLockUp .$Item;
            $sMangerPath = $sDir . $Item;

            if (strlen($Item) > 3){
                if (is_file($sPath)){
                    if (empty($sType) || self::getFileType($sPath) === $sType){
                        $aOutcome[] =
                            [
                                'text' => $Item,
                                'dir' => $sMangerPath,
                                'icon' => self::getIcon(self::getFileType($sPath)),
                                'type' => self::getFileType($sPath)
                            ];
                    }
                } else if (is_dir($sPath)){
                    $isDirEmpty = (new \FilesystemIterator($sPath))->valid();
                    $aOutcome[] =
                        [
                            'text' => $Item,
                            'children' => $isDirEmpty,
                            'dir' => $sMangerPath . '/',
                            'icon' => self::getIcon(),
                            'type' => 'dir'
                        ];
                }
            }

        }
        if (is_null($sDir)){
            return                         [
                'text' => 'Main',
                'children' => $aOutcome,
                'dir' => '',
                'icon' => self::getIcon(),
                'type' => 'dir'
            ];
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
                case "ico":
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


    public function createDir($sDir,$sName){
        $sNewDir = __FILES__ . $sDir . $sName;
        if (!is_dir($sNewDir) && !is_file($sNewDir) && is_dir(__FILES__ . $sDir)){
            if (!mkdir($sNewDir)){
                throw new \Exception('Dir creation has failed', 500);
            }
        } else {
            throw new \Exception('Dir already exits', 500);
        }
        return true;
    }

    public function deleteItem($sPath){
        if (is_file($sPath)){
            unlink($sPath);
        } else if (is_dir($sPath)){
            foreach (scandir($sPath) as $item){
                if (strlen($item) > 3){
                    $this -> deleteItem($sPath .'/'. $item);
                }
            }
            rmdir($sPath);
        } else {
            throw new \Exception('item does not exists', 404);
        }
        return true;
    }

    public function renameItem($sPath,$sNewName){
        $sRootPath = __FILES__ . $sPath;
        $sNewRootPath = dirname($sRootPath) .'/'. $sNewName;
        if (is_dir($sRootPath) || is_file($sRootPath)){
            if (is_file($sRootPath)){
                $sNewRootPath .= '.' . pathinfo($sRootPath, PATHINFO_EXTENSION);
            }
            if (rename($sRootPath,$sNewRootPath)){
                if (is_dir($sNewRootPath)){
                    return $this ->loadDir($sNewRootPath);
                } else {
                    return [
                        'text' => basename($sNewRootPath),
                        'icon' => self::getIcon(self::getFileType($sNewRootPath)),
                        'type' => self::getFileType($sNewRootPath)
                    ];
                }

            }
        }
        throw new \Exception('item does not exists', 404);
    }

    public function moveItem($sPathItem, $sNewDesignation){
    $sRootPathItem = __FILES__ . $sPathItem;
    $sNewRootPath = __FILES__ . $sNewDesignation;
    if (is_dir($sNewRootPath)){
        if (is_file($sRootPathItem)){
            $sNewRootPath .= pathinfo($sRootPathItem, PATHINFO_BASENAME);
        }
        if (rename($sRootPathItem,$sNewRootPath)){
            if (is_file($sNewRootPath)){
                return $sNewDesignation . pathinfo($sNewRootPath, PATHINFO_BASENAME);
            } else {
                return $sNewDesignation;
            }

        }
    }
    throw new NotAcceptable();
    }
}