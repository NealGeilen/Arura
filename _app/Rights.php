<?php
class Rights{
    const ARURA_USERS = 1;
    const ARURA_ROLLES = 2;
    const ARURA_SETTINGS = 3;
    const ARURA_UPDATER = 4;

    const CMS_PAGES = 10;
    const CMS_MENU = 11;

    const SHOP_PAYMENTS = 20;
    const SHOP_PRODUCTS_MANAGEMENT = 21;
    const SHOP_EVENTS_MANAGEMENT = 22;
    const SHOP_EVENTS_REGISTRATION = 23;

    const FILES_READ = 30;
    const FILES_EDIT = 31;
    const FILES_UPLOAD = 32;

    const SECURE_ADMINISTRATION = 40;
    const SECURE_ADMINISTRATION_CREATE = 41;



    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

}