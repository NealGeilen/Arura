<?php
class Rights{
    const ARURA_USERS = 1;
    const ARURA_ROLLES = 2;
    const ARURA_SETTINGS = 3;

    const CMS_PAGES = 10;
    const CMS_MENU = 11;
    const CMS_ = 12;

    const FILES_READ = 20;
    const FILES_WRITE = 21;
    const FILES_UPLOAD = 22;

    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

}