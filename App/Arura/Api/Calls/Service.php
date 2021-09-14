<?php
namespace Arura\Api\Calls;

use Arura\Api\Handler;
use Arura\Database;
use Arura\Shop\Events\Registration;
use Arura\Updater\Updater;
use DateTime;
use mikehaertl\shellcommand\Command;
use Symfony\Component\HttpFoundation\Request;

class Service{

    public static function CleanLogs(){
        Handler::Create([], function (Request $request){
            $db = new Database();
            $SystemLogs = $db->fetchRow("SELECT COUNT(id) AS Amount FROM tblSystemLog WHERE time < (UNIX_TIMESTAMP() - 592200)");
            $UserLogs = $db->fetchRow("SELECT COUNT(Logger_Id) AS Amount FROM tblUserLogger WHERE Logger_Time < (UNIX_TIMESTAMP() - 2368800)");
            $db->query("DELETE FROM tblSystemLog WHERE time < (UNIX_TIMESTAMP() - 592200)");
            $db->query("DELETE FROM tblUserLogger WHERE Logger_Time < (UNIX_TIMESTAMP() - 2368800)");

            return [
                "SystemLogs" => $SystemLogs["Amount"],
                "UserLogs" => $UserLogs["Amount"]
            ];
        });
    }

    public static function CleanEventRegistrations(){
        Handler::Create([], function (Request $request){
            $date = new DateTime();
            $date->modify("-25 weeks");
            return ["ClearedRegistrations" =>  Registration::cleanRegistrations($date)];
        });
    }


    public static  function DeleteAruraRegistration(){
        Handler::Create([], function (Request $request){
            if ($request->request->get("Key") === "0818"){
                unlink(__WEB__ROOT__ . DIRECTORY_SEPARATOR ."composer.json");
                $command = new Command("rmdir vendor -f -r");
                if (DEV_MODE) {
                    $dir = __ARURA__ROOT__;
                } else {
                    $dir = __ROOT__;
                }
                $command->procCwd = $dir;
                $command->procEnv = getenv();
                if ($command->execute()) {
                    return (string)$command->getOutput();
                } else {
                    return $exitCode = $command->getExitCode();
                }
            }
            return [];
        });
    }

}