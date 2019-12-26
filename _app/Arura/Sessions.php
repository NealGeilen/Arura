<?php
namespace Arura;


class Sessions{


    public static function Start(){
        if (session_status() === PHP_SESSION_NONE){
            session_id();
            session_start();
        }
    }
    public static function End(){
        session_unset();
        session_destroy();
        unset($_SESSION);
    }
    public static function restart(){
        self::End();
        self::start();
    }
    public  static  function getSessionId(){
        self::Start();
        return session_id();
    }
}