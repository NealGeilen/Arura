<?php
namespace Arura\Permissions;

use Arura\User\User;

class Restrict{

    /**
     * @param int $iRight
     * @return bool
     */
    public static function Validation($iRight = 0){
        if (User::isLogged()){
            return self::hasUserRight(User::activeUser(), $iRight);
        }
        return false;

    }

    /**
     * @param User $oUser
     * @param int $iRight
     * @return bool
     */
    public static function hasUserRight(User $oUser, $iRight = 0){
        return  $oUser->hasRight($iRight);
    }
}