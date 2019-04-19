<?php
define('__ROOT__',              $_SERVER['DOCUMENT_ROOT']);
define('__APP__',               __ROOT__ . '/_app/');
define('__ADDONS__',            __ROOT__ . '/_Addons/');
define('__VENDOR__',            __ROOT__ . '/vendor/');
define('__RESOURCES__',         __APP__  . 'Resources/');
define('__SITE__',              realpath(__ROOT__ . '/../'));
define('__APP_ROOT__',          realpath(__ROOT__ . '/../../'));
define('__SETTINGS__',          __APP_ROOT__ . '/settings/');
define('__FILES__',             __SITE__ . '/files/');
