<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \Arura\Client\ResponseHandler();
$request = new \Arura\Client\RequestHandler();

$request->setRight(Rights::ARURA_ROLLES);
$request->setRequestMethod('POST');

$request->sandbox(function ($aData) use ($response){
    switch ((string)$aData['type']){
        case 'get-roles':
            $a = [];
            foreach (\Arura\Permissions\Role::getAllRoles() as $oRole){
                $a[] = $oRole->__ToArray();
            }
            $response->exitSuccess($a);
            break;
        case 'get-avalibel-rights':
            $role = new \Arura\Permissions\Role((int)$aData['Role_Id']);
            $response->exitSuccess($role->getAvailableRights());
            break;
        case 'save-role':
            $role = new \Arura\Permissions\Role((int)$aData['Role_Id']);
            $role->load();
            $role->setName($aData['Role_Name']);
            if (!$role->save()){
                throw new Exception('',500);
            } else {
                $response->exitSuccess($role->__toArray());
            }
            break;

        case 'assign-right':
            $role = new \Arura\Permissions\Role((int)$aData['Role_Id']);
            $role->assignToRight((int)$aData['Right_Id']);
            break;

        case 'remove-right':
            $role = new \Arura\Permissions\Role((int)$aData['Role_Id']);
            $role->removeFromRight((int)$aData['Right_Id']);
            break;
        case 'delete-role':
            $role = new \Arura\Permissions\Role((int)$aData['Role_Id']);
            $response->exitSuccess($role->removeRole());
            break;
        case 'create-role':
            $role = \Arura\Permissions\Role::createRole($aData['Role_Name']);
            if (empty($role)){
                throw new Exception('',500);
            } else {
                $response->exitSuccess($role->__toArray());
            }
            break;
    }
});
$response->exitScript();
