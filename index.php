<?php

use NG\User\User;

require_once __DIR__ . "/_app/autoload.php";
if(!User::isLogged()){
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
    exit;
} else {
    header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
}