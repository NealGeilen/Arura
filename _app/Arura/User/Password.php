<?php
namespace NG\User;
class Password{

    public static function Create($pw){
        return password_hash($pw, PASSWORD_BCRYPT);
    }

    public static function Verify($pw, $hash){
        return password_verify($pw, $hash);
    }
}