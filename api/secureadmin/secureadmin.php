<?php
require_once __DIR__ . '/../../_app/autoload.php';
$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();
$response->isDebug(true);
$request->TriggerEvent();
$request->setRequestMethod('POST');
$request->setRight(Rights::SECURE_ADMINISTRATION_CREATE);
$request->sandbox(function ($aData) use ($response, $request){
    $request->addType("add-user", function ($aData){
        $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
        $oTable->shareTable(new \Arura\User\User($aData["User_Id"]), 1);
    });
    $request->addType("remove-user", function ($aData){
        $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
        $oTable->removeUserShare(new \Arura\User\User($aData["User_Id"]));
    });
    $request->addType("drop-table", function ($aData){
        $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
        if (!$oTable->Drop()){
            throw new Error();
        }
    });
    $request->addType("set-right-user", function ($aData){
        $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
        $oTable->setUserRights(new \Arura\User\User($aData["User_Id"]), $aData["Right"]);
    });
    $request->addType("save-table", function ($aData){
        $oTable = new Arura\SecureAdmin\SecureAdmin($aData["Table_Id"]);
        if ($oTable->isUserOwner(\Arura\User\User::activeUser())){
            $db = new \Arura\Database();
            $db -> updateRecord("tblSecureAdministration", $aData, "Table_Id");
        }
    });
    foreach ($_FILES as $file){
        if ($file["type"] === "application/json"){
            $oSecure = \Arura\SecureAdmin\SecureAdmin::Create(json_array_decode(file_get_contents($file["tmp_name"])), \Arura\User\User::activeUser());
        }
    }
});

$response->exitScript();