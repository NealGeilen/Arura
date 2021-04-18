<?php
namespace Arura\Annotation;

class Annotation{

    protected static $Annotations = [];


    /**
     * @param string $name
     * @param array $options
     * @param callable $callback
     * @return array
     */
    public static function addAnnotation($name, $options, callable $callback){
        self::$Annotations[$name] = ["value" => "", "options" => $options, "callback", $callback];
        return self::$Annotations;
    }

    /**
     * @param Method $method
     */
    public static function executeAnnotation(Method $method){
        self::$Annotations[$method->getName()]["callback"]($method->getValue(), $method->getOptions());
    }



}