<?php
namespace Arura\Pages\CMS;

use Exception;

class Handler{

    protected $Template = null;


    public function __construct(){
    }

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
            NotifyException($e);
        }
    }


    public function setTemplate($Template){
        $this->Template = $Template;
    }

    public function exitScript(){
        return  $this->Template;
    }



}