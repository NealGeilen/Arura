<?php
namespace Arura;

class Flasher{
    private static $SESSION = "Arura-Flasher";
    const Frontend = "Frontend";
    const Backend = "Backend";

    const Success = "success";
    const Error= "error";
    const Info = "info";

    /**
     * @param string $type
     * @param string $message
     * @param string $system
     */
    public static function addFlash($message = "",$type = self::Success, $system = self::Backend){
        $_SESSION[self::$SESSION][$system][] = ["type" => $type, "message" => $message];
    }

    public static function getFlashes($system = self::Backend){
        $sHtml = "";
        foreach ($_SESSION[self::$SESSION][$system] as $index => $flash){
            switch ($flash["type"]){
                case self::Info:
                    $sHtml .=  "<div class='alert alert-info bg-info'>{$flash["message"]}<button type='button' class='close' data-dismiss='alert' aria-label='Close'><i class='fas fa-times'></i></button></div>";
                    break;
                case self::Error:
                    $sHtml .= "<div class='alert alert-danger bg-danger'>{$flash["message"]}<button type='button' class='close' data-dismiss='alert' aria-label='Close'><i class='fas fa-times'></i></button></div>";
                    break;
                case self::Success:
                default:
                    $sHtml .="<div class='alert alert-success bg-success'>{$flash["message"]}<button type='button' class='close' data-dismiss='alert' aria-label='Close'><i class='fas fa-times'></i></button></div>";
                    break;
            }
            unset($_SESSION[self::$SESSION][$system][$index]);
        }
        return $sHtml;
    }
}