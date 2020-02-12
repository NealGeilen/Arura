<?php

use Arura\User\Recovery;
use Arura\User\User;
if (!isset($_GET["i"]) || !Recovery::isTokenValid($_GET["i"])){
    if(!User::isLogged()){
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
        exit;
    } else {
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
    }
}
