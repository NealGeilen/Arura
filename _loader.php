<?php
require_once __DIR__ . "/_app/autoload.php";
$smarty = new Smarty();
$aRoots = explode('.', $_GET['_url_']);
$aPath = explode('/', $aRoots[0]);
unset($aRoots[0]);
$aRoots = array_values($aRoots);

global $aUrl;
global $aHeaders;
$aUrl = $aPath;
$aHeaders = $aRoots;
if (join('/', $aUrl) === 'login'){
    $smarty->display('_Templates/Pages/login.index.html');
    exit;
}
if (!\NG\User\User::isLogged()){
    header('Location: /login');
    exit;
}




include "web-controller.php";
