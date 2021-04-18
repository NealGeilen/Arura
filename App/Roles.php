<?php
class Roles{
    const CONTENT_MANGER = 0;
    const FILE_MANGER = 1;
    const SECURE_ADMIN_USER = 2;
    const SECURE_ADMIN_MANGER = 3;
    const EVENT_MANGER = 4;
    const EVENT_USER = 5;
    const PAYMENTS = 6;
    const SYSTEM_ADMIN = 7;
    const GALLERY = 8;
    const ANALYTICS = 9;
    const REDIRECTS = 10;
    const ADDONS = 11;

    const ROLES = [
        self::CONTENT_MANGER => [
            "Name" => "Content beheerder",
            "Rights" => [Rights::CMS_MENU, Rights::CMS_PAGES, Rights::FILES_EDIT, Rights::FILES_READ, Rights::FILES_UPLOAD]
        ],
        self::FILE_MANGER => [
            "Name" => "Bestand beheerder",
            "Rights" => [Rights::FILES_EDIT, Rights::FILES_READ, Rights::FILES_UPLOAD]
        ],
        self::SECURE_ADMIN_USER => [
            "Name" => "Beveiligde administartie gebruiker",
            "Rights" => [Rights::SECURE_ADMINISTRATION]
        ],
        self::SECURE_ADMIN_MANGER =>[
            "Name" => "Beveiligde administartie beheerder",
            "Rights" => [Rights::SECURE_ADMINISTRATION_CREATE]
        ],
        self::EVENT_MANGER => [
            "Name" => "Evenementen beheerder",
            "Rights" => [Rights::FILES_READ, Rights::SHOP_EVENTS_MANAGEMENT]
        ],
        self::EVENT_USER => [
            "Name" => "Evenementen controleur",
            "Rights" => [Rights::SHOP_EVENTS_REGISTRATION, Rights::SHOP_EVENTS_VALIDATION]
        ],
        self::PAYMENTS => [
            "Name" => "Betalingen",
            "Rights" => [Rights::SHOP_PAYMENTS]
        ],
        self::SYSTEM_ADMIN => [
            "Name" => "Systeem Admin",
            "Rights" => [Rights::ARURA_USERS, Rights::ARURA_SETTINGS, Rights::ARURA_UPDATER, Rights::ARURA_WEBHOOK, Rights::ARURA_LOGS]
        ],
        self::GALLERY => [
            "Name" => "Album's",
            "Rights" => [Rights::GALLERY_MANGER]
        ],
        self::ANALYTICS => [
            "Name" => "Analytics",
            "Rights" => [Rights::ANALYTICS]
        ],
        self::REDIRECTS => [
            "Name" => "Url omleidingen",
            "Rights" => [Rights::REDIRECTS]
        ],
        self::ADDONS => [
            "Name" => "Addons",
            "Rights" => [Rights::CMS_ADDONS]
        ]
    ];
}