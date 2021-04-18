<?php
namespace Arura\Pages\CMS;

use Arura\Exceptions\MethodNotAllowed;
use Arura\SystemLogger\SystemLogger;
use Exception;

class Handler{

    /**
     * @param $sFile
     * @return bool
     */
    public static function addCssFile($sFile){
        if (!in_array($sFile, Page::$pageJsCssFiles['css'])){
            Page::$pageJsCssFiles['css'][] = $sFile;
            return true;
        }
        return false;
    }

    /**
     * @param $sFile
     * @return bool
     */
    public static function addJsFile($sFile){
        if (!in_array($sFile, Page::$pageJsCssFiles['js'])){
            Page::$pageJsCssFiles['js'][] = $sFile;
            return true;
        }
        return false;
    }

    /**
     * @param callable $callback
     */
    public static function sandbox(callable $callback){
        try{
            $callback(Page::getSmarty());
        } catch (Exception $e){
            SystemLogger::AddException(SystemLogger::Addon, $e);
        }
    }



}