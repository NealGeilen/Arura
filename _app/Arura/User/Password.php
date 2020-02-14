<?php
namespace Arura\User;
class Password{

    /**
     * @param $pw
     * @return false|string|null
     */
    public static function Create($pw){
        return password_hash($pw, PASSWORD_BCRYPT);
    }

    /**
     * @param $pw
     * @param $hash
     * @return bool
     */
    public static function Verify($pw, $hash){
        return password_verify($pw, $hash);
    }
}