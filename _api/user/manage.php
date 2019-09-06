<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();
;
$request->setRight(Rights::ARURA_USERS);
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    switch ((string)$aData['type']){
        case 'get-users':
            $UserData = [];
            foreach (\NG\User\User::getAllUsers() as $User){
                $UserData[] = $User->__ToArray();
            }
            $response->exitSuccess($UserData);
            break;
        case 'get-sessions':
            $db = new \NG\Database();
            $response->exitSuccess($db ->fetchAll('SELECT S.Session_Id, U.User_Username, FROM_UNIXTIME(S.Session_Last_Active) AS Session_Last_Active FROM tblSessions AS S JOIN tblUsers AS U ON S.Session_User_Id = U.User_Id'));
            break;
        case 'get-avalibel-roles':
            $oUser = new \NG\User\User((int)$aData['User_Id']);
            $response->exitSuccess($oUser->getAvailableRoles());
            break;
        case 'save-user':
            $oUser = new \NG\User\User((int)$aData['User_Id']);
            $oUser->load(true);
            $oUser->setEmail($aData['User_Email']);
            $oUser->setFirstname($aData['User_Firstname']);
            $oUser->setLastname($aData['User_Lastname']);
            $oUser->setUsername($aData['User_Username']);

            if ($aData['User_Password_1'] === $aData['User_Password_2'] && !empty($aData['User_Password_1'])){
                $oUser->setPassword(\NG\User\Password::Create($aData['User_Password_1']));
            }
            $oUser->save();

            $response->exitSuccess($aData);
            break;

        case 'assign-role':
            $oUser = new \NG\User\User((int)$aData['User_Id']);
            $oUser->assignToRole((int)$aData['Role_Id']);
            break;

        case 'remove-role':
            $oUser = new \NG\User\User((int)$aData['User_Id']);
            $oUser->removeFromRole((int)$aData['Role_Id']);
            break;
        case 'delete-user':
            $oUser = new \NG\User\User((int)$aData['User_Id']);
            $oUser->removeUser();

            break;
        case 'delete-session':
            $db = new \NG\Database();
            $db -> query('DELETE FROM tblSessions WHERE Session_Id = :Session_Id',
                [
                    'Session_Id' => $aData['Session_Id']
                ]);
            break;


        case 'create-user':

            $pw1= $aData['User_Password_1'];
            $pw2 = $aData['User_Password_2'];
            if ($pw2 !== $pw1){
                throw new \NG\Exceptions\NotAcceptable();
            }
            $pw = \NG\User\Password::Create($pw1);
            $oUser = \NG\User\User::createUser($aData['User_Username'], $aData['User_Firstname'], $aData['User_Lastname'],$aData['User_Email'],$pw);
            $response->exitSuccess(['User' => $oUser->__toArray(),'Roles'=>$oUser->getAvailableRoles()]);
            break;
    }
});
$response->exitScript();

