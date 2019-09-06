<?php

use NG\User\User;

require_once __DIR__ . "/_app/autoload.php";
if(!User::isLogged()){
    header("Location: /login");
} else {
    header("Location: /content/pagina");
}