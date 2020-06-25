<?php

use Arura\Sessions;
use Arura\Settings\Application;

define("__ARURA__DIR_NAME__", "dashboard");
define('__ROOT__',              $_SERVER['DOCUMENT_ROOT']);
define('__WEB__ROOT__',         $_SERVER['DOCUMENT_ROOT']);
require_once __WEB__ROOT__ . "/_config.php";


define('__ARURA__ROOT__', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__);

define("__ARURA_TEMPLATES__",   __ARURA__ROOT__ .DIRECTORY_SEPARATOR. '_Templates' . DIRECTORY_SEPARATOR);
define("__WEB_TEMPLATES__",     __WEB__ROOT__   . DIRECTORY_SEPARATOR . "Templates" . DIRECTORY_SEPARATOR);
define('__APP__',               __ARURA__ROOT__ . DIRECTORY_SEPARATOR. '_app'       . DIRECTORY_SEPARATOR);
define('__VENDOR__',            __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'vendor'     . DIRECTORY_SEPARATOR);
define('__SETTINGS__',          __APP_ROOT__    . DIRECTORY_SEPARATOR. 'settings'   . DIRECTORY_SEPARATOR);
define('__FILES__',             __WEB__ROOT__   . DIRECTORY_SEPARATOR. 'files'      . DIRECTORY_SEPARATOR);
define('__DEAFULT_PAGES__',           __ARURA__ROOT__ . DIRECTORY_SEPARATOR .'_DEAFULT_PAGES' . DIRECTORY_SEPARATOR);


define('__RESOURCES__',         __WEB__ROOT__   . DIRECTORY_SEPARATOR . "_app" . DIRECTORY_SEPARATOR     . 'Resources'  . DIRECTORY_SEPARATOR);
define('__DATAFILES__',         __ARURA__ROOT__   . DIRECTORY_SEPARATOR . '_app/Resources/DataFiles'  . DIRECTORY_SEPARATOR);

$GLOBALS["Querys"] = [];

session_set_cookie_params(0, "/", "");
Sessions::Start();
if ((int)Application::get("arura", "Debug")){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}