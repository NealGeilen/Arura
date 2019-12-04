<?php
require_once __DIR__ . '/../_app/autoload.php';
$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
$response->isDebug(true);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SECURE_ADMINISTRATION_CREATE);
$request->sandbox(function ($aData) use ($response){
    switch ($_GET["type"]){
        case "add-user":
            $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
            $oTable->shareTable(new \NG\User\User($aData["User_Id"]), 1);
            break;
        case "remove-user":
            $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
            $oTable->removeUserShare(new \NG\User\User($aData["User_Id"]));
            break;
        case "set-right-user":
            $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
            $oTable->setUserRights(new \NG\User\User($aData["User_Id"]), $aData["Right"]);
            break;
        case "save-table":
            $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
            if ($oTable->isUserOwner(\NG\User\User::activeUser())){
                $db = new \NG\Database();
                $db -> updateRecord("tblSecureAdministration", $aData, "Table_Id");
                $response->exitSuccess($db->isQuerySuccessful());
            }
            break;
        case "drop-table":
            break;
        default:
            foreach ($_FILES as $file){
                if ($file["type"] === "application/json"){
                    $oSecure = \Arura\SecureAdmin\SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), \NG\User\User::activeUser());
                }
            }
            break;
    }
});

$response->exitScript();