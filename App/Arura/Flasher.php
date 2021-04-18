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

    /**
     * @param string $system
     * @return array|null
     */
    public static function getFlashes($system = self::Backend){
        if (isset($_SESSION[self::$SESSION][$system])){
            $aData = [];
            foreach ($_SESSION[self::$SESSION][$system] as $index => $flash){
                $aData[$flash["type"]][] = $flash["message"];
                unset($_SESSION[self::$SESSION][$system][$index]);
            }
            return $aData;
        }
        return null;
    }
}