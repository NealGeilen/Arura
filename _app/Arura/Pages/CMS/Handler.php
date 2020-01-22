<?php
namespace Arura\Pages\CMS;

class Handler{

    protected $Template = null;


    public function __construct(){
    }

    public function addCssFile($sFile){
        if (!in_array($sFile, Page::$pageJsCssFiles['Css'])){
            Page::$pageJsCssFiles['Css'][] = $sFile;
            return true;
        }
        return false;
    }

    public function addJsFile($sFile){
        if (!in_array($sFile, Page::$pageJsCssFiles['Js'])){
            Page::$pageJsCssFiles['Js'][] = $sFile;
            return true;
        }
        return false;
    }

    public function sandbox(callable $callback){
        try{
            $callback(
                $_GET['PluginData']['Content'],
                $_GET['PluginData']['Addon']
            );
        } catch (\Exception $e){
            var_dump($e);
        }
    }


    public function setTemplate($Template){
        $this->Template = $Template;
    }

    public function exitScript(){
        return  $this->Template;
    }



}