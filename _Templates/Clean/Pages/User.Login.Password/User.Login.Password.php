<?php
use NG\User\User;
if (!isset($_GET["i"]) || !\NG\User\Recovery::isTokenValid($_GET["i"])){
    if(!User::isLogged()){
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
        exit;
    } else {
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
    }
}
return \Arura\Dashboard\Page::getHtml(__DIR__);
