<?php
require_once __DIR__ . '/../../_app/autoload.php';


$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

$request->setRight(Rights::ARURA_ROLLES);
$request->setRequestMethod('POST');

$request->sandbox(function ($aData) use ($response){
    switch ($aData['type']){
        case 'create-role':
            \NG\Permissions\Role::Create(htmlentities($aData['name']));
            $response->exitSuccess(true);
            break;
        case 'add-right-to-role':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            $iRightId = (int)htmlentities($aData['RightId']);
            \NG\Permissions\Management::assignRightToRole($iRoleId,$iRightId);
            $response->exitSuccess(true);
            break;
        case 'remove-right-from-role':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            $iRightId = (int)htmlentities($aData['RightId']);
            \NG\Permissions\Management::removeRightFromRole($iRoleId,$iRightId);
            $response->exitSuccess(true);
            break;
        case 'get-role-data':
            $iRoleId = htmlentities($aData['id']);
            $a = new \NG\Permissions\Role($iRoleId);
            $data['Role_Name'] = $a->getName();
            $data['Rights'] = [];
            foreach ($a->getRights() as $oRight){
                $data['Rights'][] = ['Right_Id' => $oRight->getId(), 'Right_Name' => $oRight->getName()];
            }
            $response->exitSuccess($data);
            break;
        case 'get-available-rights':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            $db = new \NG\Database();
            $data = $db->fetchAll('SELECT * FROM tblRights');
            $response->exitSuccess($data);
            break;
        case 'get-available-roles':
            $iUserId = (int)htmlentities($aData['UserId']);
            $db = new \NG\Database();
            $data = $db->fetchAll('SELECT * FROM tblRoles');
            $response->exitSuccess($data);
            break;
        case 'set-role-name':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            $sName = htmlentities($aData['RoleName']);
            \NG\Database::ExecQuery('UPDATE tblRoles SET Role_Name = ? WHERE Role_Id = ? ',
                [
                    $sName,
                    $iRoleId
                ]);
            break;
        case 'delete-role':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            \NG\Database::ExecQuery('DELETE FROM tblRoles WHERE Role_Id = ?', [$iRoleId]);
            \NG\Database::ExecQuery('DELETE FROM tblRoleRights WHERE Role_Id = ?', [$iRoleId]);
            \NG\Database::ExecQuery('DELETE FROM tblUserRoles WHERE Role_Id = ?', [$iRoleId]);
            $response->exitSuccess(true);
            break;
    }
});
$response->exitScript();
