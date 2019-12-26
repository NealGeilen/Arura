<?php
require_once __DIR__ . '/../../_app/autoload.php';

if (isset($_GET["type"]) && $_GET["type"] = "export" && \Arura\Permissions\Restrict::Validation(Rights::SECURE_ADMINISTRATION)){
    $oTable = new Arura\SecureAdmin\SecureAdmin($_GET["Table_Id"]);
    if ($oTable->hasUserRight(\Arura\User\User::activeUser(), \Arura\SecureAdmin\SecureAdmin::EXPORT)){
        $oTable->Export();
    }
}