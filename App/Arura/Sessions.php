<?php
namespace Arura;


class Sessions{


    /**
     *
     */
    public static function Start(){
        if (session_status() === PHP_SESSION_NONE){
            if (ini_get("session.use_strict_mode") !== 1){
                ini_set('session.use_strict_mode', 1);
            }
            session_start();
            session_id();
        }
    }

    /**
     *
     */
    public static function End(){
        session_unset();
        session_destroy();
        unset($_SESSION);
        unset($_SESSION);
        unset($_SESSION);
        unset($_SESSION);
    }

    /**
     *
     */
    public static function restart(){
        self::End();
        self::start();
    }

    /**
     * @return string
     */
    public  static  function getSessionId(){
        self::Start();
        return session_id();
    }
}