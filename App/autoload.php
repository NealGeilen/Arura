<?php

use Arura\Pages\Page;
use Arura\Sessions;
use Arura\Settings\Application;
use Arura\SystemLogger\SystemLogger;

if (isset($_SERVER["PHPRC"])){
    define('__ROOT__', $_SERVER["PHPRC"]);
} else {
    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}

require_once __ROOT__ . "/_config.php";


define("__ARURA__DIR_NAME__", "dashboard");

define('__WEB__ROOT__',         __ROOT__);



date_default_timezone_set("Europe/Amsterdam");

if (!defined("DEV_MODE")){
    define("DEV_MODE", false);
}

if (!defined("DEBUG_MODE")){
    define("DEBUG_MODE", false);
}
define('__SETTINGS__',          __APP_ROOT__    . DIRECTORY_SEPARATOR. 'settings'   . DIRECTORY_SEPARATOR);

if (DEV_MODE){
    define('__ARURA__ROOT__',       __WEB__ROOT__ .  DIRECTORY_SEPARATOR .  __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR);
    define('__VENDOR__',            __ARURA__ROOT__. 'vendor'     . DIRECTORY_SEPARATOR);
    if(is_file(__VENDOR__ . "autoload.php")){
        require_once __VENDOR__ . "autoload.php";
    } else  {
        throw new Error("composer not installed", 500);
    }
} else {
    define('__VENDOR__',            __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'vendor'     . DIRECTORY_SEPARATOR);
    define('__ARURA__ROOT__',       __VENDOR__ ."arura". DIRECTORY_SEPARATOR .  __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR);
}


define("__ARURA_TEMPLATES__",   __ARURA__ROOT__ . '_Templates' . DIRECTORY_SEPARATOR);
define('__APP__',               __ARURA__ROOT__ . 'App'       . DIRECTORY_SEPARATOR);
define('__DATAFILES__',         __APP__ . 'Resources/DataFiles'  . DIRECTORY_SEPARATOR);
define('__DEFAULT_PAGES__',     __ARURA__ROOT__ .'_DEFAULT_PAGES' . DIRECTORY_SEPARATOR);
define('__CONTROLLERS__',       __APP__  .'Controllers' . DIRECTORY_SEPARATOR);

define('__FILES__',             __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'files'      . DIRECTORY_SEPARATOR);
define("__WEB_TEMPLATES__",     __WEB__ROOT__   . DIRECTORY_SEPARATOR . "Templates" . DIRECTORY_SEPARATOR);
define('__RESOURCES__',         __APP__ .  'Resources'  . DIRECTORY_SEPARATOR);

define("__STANDARD_MODULES__" , __ARURA_TEMPLATES__ . "Modules" . DIRECTORY_SEPARATOR);
define("__CUSTOM_MODULES__", __WEB_TEMPLATES__ . "Custom" . DIRECTORY_SEPARATOR. "Modules". DIRECTORY_SEPARATOR);
Page::$smarty = new Smarty();
Sessions::Start();
if ((int)Application::get("arura", "Debug")){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    SystemLogger::ErrorHandler(SystemLogger::System);
}
