<?php

define("__ARURA__DIR_NAME__", "dashboard");
define('__ROOT__',              $_SERVER['DOCUMENT_ROOT']);
define('__WEB__ROOT__',         $_SERVER['DOCUMENT_ROOT']);
define('__APP_ROOT__',          realpath(__WEB__ROOT__ . '/../../'));

define('__ARURA__ROOT__', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__);

define("__ARURA__TEMPLATES__", __ARURA__ROOT__  .DIRECTORY_SEPARATOR. '_Templates' . DIRECTORY_SEPARATOR);

define('__APP__',               __ARURA__ROOT__ . DIRECTORY_SEPARATOR. '_app'       . DIRECTORY_SEPARATOR);
define('__VENDOR__',            __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'vendor'     . DIRECTORY_SEPARATOR);
define('__SETTINGS__',          __APP_ROOT__    . DIRECTORY_SEPARATOR. 'settings'   . DIRECTORY_SEPARATOR);
define('__FILES__',             __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'files'      . DIRECTORY_SEPARATOR);


define('__RESOURCES__',         __APP__         . 'Resources'  . DIRECTORY_SEPARATOR);
define('__DATAFILES__',         __RESOURCES__   . DIRECTORY_SEPARATOR. 'DataFiles'  . DIRECTORY_SEPARATOR);


require_once __VENDOR__ . "autoload.php";
require_once __APP__    . "Rights.php";