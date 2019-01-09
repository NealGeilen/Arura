<?php
require_once __DIR__ . '/../../_app/autoload.php';

$response = new \NG\Client\ResponseHandler();
$request = new \NG\Client\RequestHandler();

//$request->setRight();
$request->setRequestMethod('POST');
$request->sandbox(function ($aData) use ($response){
    switch ($aData['type']){
        case 'create':
            $aUserData = $aData['InputData'];
            $username =         htmlentities($aUserData['User_Email']);
            $firstname =        htmlentities($aUserData['User_Firstname']);
            $lastname =         htmlentities($aUserData['User_Lastname']);
            $email =            htmlentities($aUserData['User_Email']);
            if ($aUserData['password1'] === $aUserData['password2'] && $aUserData['password2'] !== '' && $aUserData['password1'] !== ''){
                $password = \NG\User\Password::Create(htmlentities($aUserData['password2']));
            } else {
                $password = \NG\User\Password::Create(\NG\Functions::str_random(128));
            }
            \NG\User\User::createUser($username,$firstname,$lastname,$email,$password);
            break;
        case 'user-data':
            $id = htmlentities($aData['id']);
            $db = new \NG\Database();
            $user_details = $db -> fetchRow('SELECT User_Email, User_Firstname,User_Lastname,User_Username FROM tblUsers WHERE User_Id = ?',[$id]);
            $user_details['Roles'] = $db -> fetchAll('SELECT t2.Role_Id, t2.Role_Name FROM tblUserRoles as t1 JOIN tblRoles as t2 ON t1.Role_Id = t2.Role_Id WHERE t1.User_Id = ? ',
                [
                    $id
                ]);
            $response->exitSuccess($user_details);
            break;
        case 'update':
            $aUserData = $aData['InputData'];
            $usr_email = htmlentities($aUserData['user_email']);
            $user = new \NG\User\User((string)$usr_email);
            $user -> setEmail(htmlentities($aUserData['User_Email']));
            $user -> setUsername(htmlentities($aUserData['User_Username']));
            $user -> setFirstname(htmlentities($aUserData['User_Firstname']));
            $user -> setLastname(htmlentities($aUserData['User_Lastname']));

            if ($aUserData['password1'] === $aUserData['password2'] && $aUserData['password2'] !== '' && $aUserData['password1'] !== ''){
                $user -> setPassword(\NG\User\Password::Create(htmlentities($aUserData['password1'])));
            }

            $user->save();
            break;
        case 'remove-role-from-user':
            $iRoleId = (int)htmlentities($aData['RoleId']);
            $iUserId = (int)htmlentities($aData['UserId']);
            \NG\Permissions\Management::removeRoleFromUser($iUserId,$iRoleId);
            $response->exitSuccess(true);
            break;
        case 'add-role-to-user':
            $iRoleId = (int)htmlentities($_POST['RoleId']);
            $iUserId = (int)htmlentities($_POST['UserId']);
            \NG\Permissions\Management::assignRoleToUser($iUserId,$iRoleId);
            $response->exitSuccess(true);
            break;
        case 'delete-user':
            $id = htmlentities($aData['User_Id']);
            \NG\User\User::removeUser((int)$id);
            break;
        case 'delete-session':
            $id = htmlentities($aData['id']);
            $db = new \NG\Database();
            $db -> query('DELETE FROM tblSessions WHERE Session_Id = ?',[$id]);
            break;
        case 'get-available-roles':
            $iUserId = (int)htmlentities($_POST['UserId']);
            $db = new \NG\Database();
            $aData = $db->fetchAll('SELECT * FROM tblRoles');
            $response->exitSuccess($aData);
            break;
    }
});
$response->exitScript();

