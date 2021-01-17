<?php
namespace Arura\Webhooks;

use ReflectionClass;

class Trigger{
    const EVENT_CREATE = 1;
    const EVENT_EDIT =2;
    const EVENT_PUBLISH =3;
    const EVENT_REGISTRATION_OPEN =4;
    const EVENT_REGISTRATION = 5;


    const ALBUM_CREATE = 10;
    const ALBUM_EDIT = 11;
    const ALBUM_PUBLISH = 12;


    public static function getTriggers(){
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}