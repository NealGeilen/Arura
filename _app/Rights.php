<?php
class Rights{
    const ARURA_USERS = 1;
    const ARURA_ROLLES = 2;
    const ARURA_SETTINGS = 3;

    const CMS_PAGES = 10;
    const CMS_MENU = 11;

    const EVENTS_MANGE= 20;
    const EVENTS_REGISTRATION = 21;

    const FILES_READ = 30;
    const FILES_EDIT = 31;
    const FILES_UPLOAD = 32;

    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

}