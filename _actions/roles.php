<?php
$aRoles = $db -> fetchAll('SELECT * FROM tblRoles');

foreach ($aRoles as $iKey =>  $aRole){
    $oRole = new \NG\Permissions\Role($aRole['Role_Id']);
    $aRoles[$iKey]['Role_Rights'] = [];
    foreach ($oRole->getRights() as $oRole){
        $aRoles[$iKey]['Role_Rights'][] = ['Right_Id' =>  (int)$oRole ->getId(), 'Right_Name' => $oRole->getName()];
    }

}
$aRights = $db -> fetchAll('SELECT * FROM tblRights');

$smarty->assign('aRoles', $aRoles);
$smarty->assign('aRights', $aRights);
$tContentTemplate = $smarty -> fetch(__TEMPLATES__ . 'Pages/roles.index.html');