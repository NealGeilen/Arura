<?php
namespace Arura\Permissions;

use Arura\User\User;

class Restrict{

    public static function Validation($iRight){
        if (User::isLogged()){
            return self::hasUserRight(User::activeUser(), $iRight);
        }
        return false;

    }
    public static function hasUserRight(User $oUser, $iRight){
        return  $oUser->hasRight(new Right($iRight));
    }
}