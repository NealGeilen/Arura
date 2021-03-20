<?php
namespace Arura\Api\Calls;

use Arura\Api\Handler;
use Arura\Database;
use Arura\Shop\Events\Registration;
use DateTime;
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
            Registration::cleanRegistrations($date);
        });
    }

}