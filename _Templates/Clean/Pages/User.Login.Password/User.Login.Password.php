<?php
use NG\User\User;





if (!isset($_GET["i"])){
    if(!User::isLogged()){
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
        exit;
    } else {
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
    }
}
$db = new \NG\Database();

$aData = $db -> fetchRow("SELECT * FROM tblPasswordRecovery WHERE PR_Token = :PR_Token", ["PR_Token" => $_GET["i"]]);
if (count($aData) !== 0){
    \Arura\Dashboard\Page::getSmarty()->assign("iUserId", $aData["PR_User_Id"]);
} else {
    if(!User::isLogged()){
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR."login");
        exit;
    } else {
        header("Location:" . DIRECTORY_SEPARATOR . __ARURA__DIR_NAME__ . DIRECTORY_SEPARATOR . "home");
    }
}
return \Arura\Dashboard\Page::getHtml(__DIR__);
