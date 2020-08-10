<?php
namespace Arura\User;

use Arura\Database;

class Logger{

    const CREATE =0;
    const READ = 1;
    const DELETE = 2;
    const UPDATE = 3;

    public static function getLog(string $id){
        $db = new Database();
        return $db -> fetchRow("SELECT * FROM tblUserLogger WHERE Logger_Id  = :Id", ["Id"=> $id]);
    }

    public static function Create(int $type, $entity, $name ="",&$user = null){
        if (is_null($user)){
            $user = User::activeUser();
        }
        $db = new Database();
        $aLog = [];
        if ($type === self::READ){
            $aLog = $db->fetchAll("SELECT * FROM tblUserLogger WHERE Logger_Time > :Logger_Time AND Logger_Entity = :Logger_Entity AND Logger_Name = :Logger_Name AND Logger_Type = :Logger_Type AND Logger_User_Id = :Logger_User_Id LIMIT 1",[
                "Logger_User_Id" => $user->getId(),
                "Logger_Type" => $type,
                "Logger_Name" => $name,
                "Logger_Entity" => $entity,
                "Logger_Time" => (time() - 1000)
            ]);
        }
        if (empty($aLog)){
            $db->createRecord("tblUserLogger", [
                "Logger_Id" => createGuid(),
                "Logger_User_Id" => $user->getId(),
                "Logger_Type" => $type,
                "Logger_Name" => $name,
                "Logger_Entity" => $entity,
                "Logger_Time" => time()
            ]);
        }

    }

    public static function getLogsUser(User $user){
        $db = new Database();
        $aLogs = [];
        foreach ($db -> fetchAll("SELECT *, FROM_UNIXTIME(Logger_Time, '%d-%m-%Y') as Day FROM tblUserLogger WHERE Logger_User_Id  = :Id ORDER BY Logger_Time DESC ", ["Id"=> $user->getId()]) as $log){
            $log["Entity"] = self::getEntity($log["Logger_Entity"]);
            $log["Status"] = self::getStatus($log["Logger_Type"]);
            $aLogs[$log["Day"]][] = $log;
        }
        return $aLogs;
    }

    public static function getEntity(string $entity){
        switch ($entity){
            case "Arura\User\User":
                return [
                    "Icon" => "far fa-user",
                    "Name" => "Gebruiker"
                ];
                break;
            case "Arura\Pages\CMS\Page":
                return [
                    "Icon" => "fas fa-file",
                    "Name" => "Pagina"
                ];
                break;
            case "Arura\Gallery\Gallery":
                return [
                    "Icon" => "fas fa-images",
                    "Name" => "Album"
                ];
                break;
            case "Arura\Gallery\Image":
                return [
                    "Icon" => "fas fa-image",
                    "Name" => "Afbeelding"
                ];
                break;
            case "Arura\Updater\DataBaseSync":
                return [
                    "Icon" => "fas fa-database",
                    "Name" => "Database"
                ];
                break;
            default:
                return [
                    "Icon" => "fas fa-box",
                    "Name" => $entity
                ];
                break;
        }
        return null;
    }

    public static function getStatus(int $status){
        switch ($status){
            case 1:
                return [
                    "Color" => "bg-primary",
                    "Name" => "Gelezen"
                ];
                break;
            case 0:
                return [
                    "Color" => "bg-success",
                    "Name" => "Aangemaakt"
                ];
                break;
            case 2:
                return [
                    "Color" => "bg-danger",
                    "Name" => "Verwijdert"
                ];
                break;
            case 3:
                return [
                    "Color" => "bg-warning",
                    "Name" => "Aangepast"
                ];
                break;
        }
        return null;
    }
}